<?php

namespace Tests\Dynamic;

use App\Entities\User;
use App\Entities\Topic;
use App\Entities\Dynamic;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * @group dynamic
 */
class Test extends \TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $dynamic;
    protected $dynamicFlow;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'name'     => '动态测试账号',
            'password' => 'test123'
        ]);

        $topic = factory(Topic::class)->create([]);

        $this->dynamic = factory(Dynamic::class)->create([
            'type'           => 'topic.create',
            'shareable_id'   => $topic->id,
            'shareable_type' => 'topic',
        ]);

        $this->dynamicFlow = factory(Dynamic\Flow::class)->create([
            'author_id'  => $this->dynamic->author_id,
            'dynamic_id' => $this->dynamic->id,
            'type'       => Dynamic\Flow::TYPE_NORMAL
        ]);
    }

    public function testShow()
    {
        $id = hashids_encode($this->dynamicFlow->id);

        $this->get(sprintf('/dynamic/%s', $id), [])
            ->seeStatusCode(200)
            ->seeJson([
                'id'             => $id,
                'category'       => 'topic.create',
                'is_fabulous'    => 0,
                'fabulous_count' => 0
            ]);
    }

    /**
     * @param $comment
     * @param $expected
     *
     * @dataProvider repostDataProvider
     */
    public function testRepost($comment, $expected)
    {
        $token = $this->createToken($this->user);

        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        $id = hashids_encode($this->dynamicFlow->id);

        $params = [
            'comment' => $comment
        ];

        if ($comment === false) {
            $params = [];
        }

        $this->post(sprintf('/dynamic/%s/forks', $id), $params, $headers);

        if ($expected === false) {
            $this->seeStatusCode(400);

            return;
        }

        $this->seeStatusCode(205);
        $this->seeInDatabase('dynamic_flow', [
            'type'         => Dynamic\Flow::TYPE_REPOST,
            'author_id'    => $this->user->id,
            'dynamic_id'   => $this->dynamicFlow->dynamic_id,
            'referer_id'   => $this->dynamicFlow->id,
            'content'      => $expected,
            'repost_count' => 0
        ]);

        $this->seeInDatabase('dynamic_flow', [
            'id'           => $this->dynamicFlow->id,
            'repost_count' => $this->dynamicFlow->repost_count + 1
        ]);
    }



    public function repostDataProvider()
    {
        return [
            [false, ''],
            ['哈哈哈', '哈哈哈'],
            [null, ''],
            [Str::random(501), false]
        ];
    }

    public function testRepostLevel()
    {
        $token = $this->createToken($this->user);

        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        $id = hashids_encode($this->dynamicFlow->id);

        $params = [
            'comment' => '测试转发内容'
        ];

        $this->post(sprintf('/dynamic/%s/forks', $id), $params, $headers);

        $this->seeStatusCode(205);
        $this->seeInDatabase('dynamic_flow', [
            'type'         => Dynamic\Flow::TYPE_REPOST,
            'author_id'    => $this->user->id,
            'dynamic_id'   => $this->dynamicFlow->dynamic_id,
            'referer_id'   => $this->dynamicFlow->id,
            'content'      => '测试转发内容',
            'repost_count' => 0
        ]);

        $this->seeInDatabase('dynamic_flow', [
            'id'           => $this->dynamicFlow->id,
            'repost_count' => $this->dynamicFlow->repost_count + 1
        ]);

        $id = Dynamic\Flow::where('referer_id', $this->dynamicFlow->id)->value('id');

        // 再次转发
        $this->post(sprintf('/dynamic/%s/forks', hashids_encode($id)), [
            'comment' => '测试二级转发内容'
        ], $headers);

        $this->seeInDatabase('dynamic_flow', [
            'type'         => Dynamic\Flow::TYPE_REPOST,
            'author_id'    => $this->user->id,
            'dynamic_id'   => $this->dynamicFlow->dynamic_id,
            'referer_id'   => $id,
            'content'      => sprintf('测试二级转发内容 //@%s: 测试转发内容', $this->user->name),
            'repost_count' => 0
        ]);

        $this->seeInDatabase('dynamic_flow', [
            'id'           => $id,
            'repost_count' => 1
        ]);

    }

    public function testRepostGuard()
    {
        $token = $this->createToken($this->user);

        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        $this->post(sprintf('/dynamic/%s/forks', hashids_encode(999999)), [
            'comment' => '测试不存在的动态'
        ], $headers)->seeStatusCode(404);

        Dynamic::where('id', $this->dynamic->id)->update([
            'state' => Dynamic::STATE_REMOVE
        ]);

        $this->post(sprintf('/dynamic/%s/forks', hashids_encode($this->dynamicFlow->id)), [
            'comment' => '测试已被删除的动态'
        ], $headers)->seeStatusCode(404);

        Dynamic::where('id', $this->dynamic->id)->update([
            'state' => Dynamic::STATE_NORMAL
        ]);

        Dynamic\Flow::where('id', $this->dynamicFlow->id)->update([
            'state' => Dynamic\Flow::STATE_REMOVE
        ]);

        $this->post(sprintf('/dynamic/%s/forks', hashids_encode($this->dynamicFlow->id)), [
            'comment' => '测试已被删除的动态'
        ], $headers)->seeStatusCode(404);

        Dynamic\Flow::where('id', $this->dynamicFlow->id)->update([
            'state' => Dynamic\Flow::STATE_LOCKED
        ]);

        $this->post(sprintf('/dynamic/%s/forks', hashids_encode($this->dynamicFlow->id)), [
            'comment' => '测试已被锁定的动态'
        ], $headers)->seeStatusCode(403);

        Dynamic::where('id', $this->dynamic->id)->update([
            'state' => Dynamic::STATE_LOCKED
        ]);

        Dynamic\Flow::where('id', $this->dynamicFlow->id)->update([
            'state' => Dynamic\Flow::STATE_NORMAL
        ]);

        $this->post(sprintf('/dynamic/%s/forks', hashids_encode($this->dynamicFlow->id)), [
            'comment' => '测试已被锁定的动态'
        ], $headers)->seeStatusCode(403);
    }

    /**
     * @param $type
     * @param $expected
     *
     * @dataProvider addFabulousDataProvider
     */
    public function testAddFabulous($type, $expected)
    {
        $token = $this->createToken($this->user);

        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        $id = hashids_encode($this->dynamicFlow->id);

        $this->post(sprintf('/dynamic/%s/fabulous', $id), [
            'type' => $type
        ], $headers);

        $this->seeStatusCode($expected);

        if ($expected != 205) {
            return;
        }

        $this->seeInDatabase('dynamic_fabulous', [
            'user_id' => $this->user->id,
            'flow_id' => $this->dynamicFlow->id,
            'type'    => $type
        ]);

        // 尝试重复赞

        $this->post(sprintf('/dynamic/%s/fabulous', $id), [
            'type' => $type
        ], $headers);

        $this->seeStatusCode(409);

        $this->seeInDatabase('dynamic_flow', [
            'id'             => $this->dynamicFlow->id,
            'fabulous_count' => $this->dynamicFlow->fabulous_count + 1,
            'fabulous_type'  => $this->dynamicFlow->fabulous_type | $type,
            'fabulous_user'  => $this->user->id
        ]);
    }

    public function addFabulousDataProvider()
    {
        return [
            [1, 205],
            [2, 205],
            [3, 400],
            [4, 205],
            [8, 400]
        ];
    }

    public function testDelFabulous()
    {
        $token = $this->createToken($this->user);

        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        factory(Dynamic\Fabulous::class)->create([
            'user_id' => $this->user->id,
            'flow_id' => $this->dynamicFlow->id,
            'type'    => 1
        ]);

        $this->dynamicFlow->update([
            'fabulous_count' => 1,
            'fabulous_type'  => 1,
            'fabulous_user'  => $this->user->id
        ]);

        $id = hashids_encode($this->dynamicFlow->id);

        $this->seeInDatabase('dynamic_flow', [
            'id'             => $this->dynamicFlow->id,
            'fabulous_count' => 1,
            'fabulous_type'  => 1,
            'fabulous_user'  => $this->user->id
        ]);

        $this->delete(sprintf('/dynamic/%s/fabulous', $id), [], $headers);

        $this->seeStatusCode(205);

        $this->seeInDatabase('dynamic_flow', [
            'id' => $this->dynamicFlow->id,
            'fabulous_count' => 0,
            'fabulous_type'  => 0,
            'fabulous_user'  => 0
        ]);

        $this->delete(sprintf('/dynamic/%s/fabulous', $id), [], $headers)
            ->seeStatusCode(404);
    }

    public function testNotLogin()
    {
        $this->get('/dynamics')
            ->seeStatusCode(200);

        $content = json_decode($this->response->content(), true);

        $this->assertTrue(array_get($content, '0.category') == 'topic.create');
    }
}