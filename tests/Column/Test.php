<?php

namespace Tests\Column;

use App\Entities\User;
use App\Entities\Topic;
use App\Entities\Column;
use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * @group Column
 */
class Test extends \TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $topic;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'name'     => '专栏测试账号',
            'password' => 'test123'
        ]);

        $this->topic = factory(Topic::class)->create();
    }

    public function createValidationProvider()
    {
        return [
            ['', 'test', '这是测试专栏的描述信息', true],
            ['测试话题', '', '这是测试专栏的描述信息', true],
            ['测试话题', 'test', '', true],
            ['测试话题', 'test', '这是测试', true],
            ['测试话题', 'test', '这是测试专栏的描述信息', false],
        ];
    }

    /**
     * @param $name
     * @param $link
     * @param $summary
     * @param $withTopics
     *
     * @dataProvider createValidationProvider
     */
    public function testCreateValidate($name, $link, $summary, $withTopics)
    {
        $token = $this->createToken($this->user);

        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        $this->post('/column', [
            'name'    => $name,
            'link'    => $link,
            'summary' => $summary,
            'topics'  => hashids_encode($withTopics ? $this->topic->id : 99999999)
        ], $headers);

        $this->seeStatusCode(400);
    }

    public function testCreate()
    {
        $token = $this->createToken($this->user);

        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        $data = [
            'name'    => '测试专栏',
            'link'    => 'test',
            'summary' => '这是测试专栏的描述信息',
            'topics'  => hashids_encode($this->topic->id)
        ];

        // 测试登录
        $this->post('/column', $data)
            ->seeStatusCode(401);

        // 正常
        $this->post('/column', $data, $headers)
            ->seeStatusCode(200)
            ->seeJson([
                'name'    => '测试专栏',
                'link'    => 'test',
                'summary' => '这是测试专栏的描述信息'
            ])
            ->seeJsonStructure([
                'id', 'name', 'link', 'icon', 'summary'
            ])
            ->seeInDatabase('column', [
                'name'    => '测试专栏',
                'link'    => 'test',
                'summary' => '这是测试专栏的描述信息'
            ]);

        $content = json_decode($this->response->content(), true);
        $id      = hashids_decode(array_get($content, 'id'));

        $this->seeInDatabase('dynamic', [
            'shareable_id'   => $id,
            'shareable_type' => 'column'
        ]);

        // 重复标题
        $this->post('/column', $data, $headers)
            ->seeStatusCode(400);

        // 多个话题
        $topics = factory(Topic::class, 5)->create();

        $topics = $topics->map(function ($topic) {
            return $topic->id;
        })->map('hashids_encode');

        $this->post('/column', [
            'name'    => '测试多个话题',
            'link'    => 'test_topic',
            'summary' => '测试多个话题专栏描述信息',
            'topics'  => $topics->implode(',')
        ], $headers)->seeStatusCode(200);
    }

    public function testShow()
    {
        $token = $this->createToken($this->user);

        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        $this->get(sprintf('/column/%s', hashids_encode(0)))
            ->seeStatusCode(404);

        $column = factory(Column::class)->create([
            'founder_id' => $this->user->id,
            'owner_id'   => $this->user->id
        ]);
        $column->topics()->attach($this->topic->id);

        $column->members()->attach($this->user->id, [
            'role' => Column::ROLE_OWNER
        ]);

        $this->get(sprintf('/column/%s', hashids_encode($column->id)))
            ->seeStatusCode(200)
            ->seeJson([
                'name'    => $column->name,
                'summary' => $column->summary,
                'link'    => $column->link
            ])
            ->seeJsonStructure([
                'id', 'name', 'link', 'icon', 'summary'
            ])
            ->dontSeeJson([
                'role' => Column::ROLE_OWNER
            ]);

        // 登录后增加 role 字段
        $this->get(sprintf('/column/%s', hashids_encode($column->id)), $headers)
            ->seeStatusCode(200)
            ->seeJson([
                'name'    => $column->name,
                'summary' => $column->summary,
                'link'    => $column->link,
                'role'    => Column::ROLE_OWNER
            ])
            ->seeJsonStructure([
                'id', 'name', 'link', 'icon', 'summary', 'role'
            ]);
    }

    public function testSubscribe()
    {
        $column = factory(Column::class)->create();
        $column->topics()->attach($this->topic->id);
        $column->members()->attach($this->user->id, [
            'role' => Column::ROLE_OWNER
        ]);

        $token = $this->createToken($this->user);

        $this->post(sprintf('/column/%s/subscribers', hashids_encode($column->id)), [], [
            'Authorization' => 'Bearer ' . $token
        ])->seeStatusCode(205);

        $this->seeInDatabase('column_subscriber', [
            'column_id' => $column->id,
            'user_id'   => $this->user->id
        ]);

        // 重复关注
        $this->post(sprintf('/column/%s/subscribers', hashids_encode($column->id)), [], [
            'Authorization' => 'Bearer ' . $token
        ])->seeStatusCode(400);
    }

    public function testUnsubscribe()
    {
        $column = factory(Column::class)->create();
        $column->topics()->attach($this->topic->id);
        $column->members()->attach($this->user->id, [
            'role' => Column::ROLE_OWNER
        ]);

        $column->subscriber()->attach($this->user->id);

        $token = $this->createToken($this->user);

        $this->seeInDatabase('column_subscriber', [
            'column_id' => $column->id,
            'user_id'   => $this->user->id
        ]);

        $this->delete(sprintf('/column/%s/subscribers', hashids_encode($column->id)), [], [
            'Authorization' => 'Bearer ' . $token
        ])->seeStatusCode(205);

        $this->notSeeInDatabase('column_subscriber', [
            'column_id' => $column->id,
            'user_id'   => $this->user->id
        ]);

        // 重复取消
        $this->delete(sprintf('/column/%s/subscribers', hashids_encode($column->id)), [], [
            'Authorization' => 'Bearer ' . $token
        ])->seeStatusCode(400);
    }
}