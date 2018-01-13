<?php

namespace Tests\Topic;

use App\Entities\Topic;
use App\Entities\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * @group Topic
 */
class Test extends \TestCase
{
    use DatabaseTransactions;

    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create([
            'name'     => 'test_topic',
            'password' => 'test123'
        ]);
    }

    public function createProvider()
    {
        return [
            [true, '测试话题', '这是一个测试话题', 200],
            [true, '测试话题', '', 400],
            [false, '测试话题', '', 401]
        ];
    }

    /**
     * @param $needToken
     * @param $name
     * @param $summary
     * @param $expected
     *
     * @dataProvider createProvider
     */
    public function testCreate($needToken, $name, $summary, $expected)
    {
        $token = $needToken ? $this->createToken($this->user) : '';

        $this->post('/topic', [
            'name'    => $name,
            'summary' => $summary
        ], [
            'Authorization' => 'Bearer ' .$token
        ]);

        $this->seeStatusCode($expected);

        if ($expected >= 200 and $expected < 300) {
            $this->seeJson([
                'name'          => $name,
                'summary'       => $summary,
                'user_count'    => 0,
                'article_count' => 0,
                'column_count'  => 0
            ]);

            $this->seeJsonStructure([
                'id', 'name', 'icon', 'summary',
                'user_count', 'article_count', 'column_count'
            ]);
        }
    }

    public function testRepeatCreate()
    {
        $token = $this->createToken($this->user);

        $this->post('/topic', [
            'name'    => '测试话题',
            'summary' => '这是一个测试话题'
        ], [
            'Authorization' => 'Bearer ' .$token
        ])->seeStatusCode(200);

        $this->post('/topic', [
            'name'    => '测试话题',
            'summary' => '这是一个测试话题'
        ], [
            'Authorization' => 'Bearer ' .$token
        ])->seeStatusCode(400);
    }

    public function testShow()
    {
        $topic = factory(Topic::class)->create([
            'founder_id' => $this->user->id
        ]);

        $this->get(sprintf('/topic/%s', hashids_encode($topic->id)))
            ->seeStatusCode(200)
            ->seeJson([
                'id' => hashids_encode($topic->id)
            ])
            ->seeJsonStructure([
                'id', 'name', 'icon', 'founder', 'summary',
                'user_count', 'article_count', 'column_count'
            ]);
    }

    public function testFollow()
    {
        $topic = factory(Topic::class)->create();
        $token = $this->createToken($this->user);

        $this->post(sprintf('/topic/%s/followers', hashids_encode($topic->id)), [], [
            'Authorization' => 'Bearer ' .$token
        ])->seeStatusCode(205);

        $this->seeInDatabase('topic_user', [
            'topic_id' => $topic->id,
            'user_id'  => $this->user->id
        ]);

        // 重复关注
        $this->post(sprintf('/topic/%s/followers', hashids_encode($topic->id)), [], [
            'Authorization' => 'Bearer ' .$token
        ])->seeStatusCode(409);
    }

    public function testUnfollow()
    {
        $topic = factory(Topic::class)->create();
        $topic->users()->attach($this->user->id);
        $token = $this->createToken($this->user);

        $this->seeInDatabase('topic_user', [
            'topic_id' => $topic->id,
            'user_id'  => $this->user->id
        ]);

        $this->delete(sprintf('/topic/%s/followers', hashids_encode($topic->id)), [], [
            'Authorization' => 'Bearer ' .$token
        ])->seeStatusCode(205);

        $this->notSeeInDatabase('topic_user', [
            'topic_id' => $topic->id,
            'user_id'  => $this->user->id
        ]);
    }

}