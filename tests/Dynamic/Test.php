<?php

namespace Tests\Dynamic;

use App\Entities\User;
use App\Entities\Topic;
use App\Entities\Column;
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
    protected $topicDynamic;
    protected $topicDynamicFlow;
    protected $columnDynamic;
    protected $columnDynamicFlow;
    protected $repostDynamic;
    protected $repostDynamicFlow;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'name'     => '动态测试账号',
            'password' => 'test123'
        ]);

        $topic = factory(Topic::class)->create([
            'name'    => '测试话题',
            'summary' => '测试话题的描述信息'
        ]);

        $this->topicDynamic = factory(Dynamic::class)->create([
            'type'           => 'topic.create',
            'shareable_id'   => $topic->id,
            'shareable_type' => 'topic',
        ]);

        $this->topicDynamicFlow = factory(Dynamic\Flow::class)->create([
            'author_id'  => $this->topicDynamic->author_id,
            'dynamic_id' => $this->topicDynamic->id,
            'type'       => Dynamic\Flow::TYPE_NORMAL
        ]);

        $column = factory(Column::class)->create([
            'name'    => '测试专栏',
            'summary' => '测试专栏的描述信息'
        ]);

        $this->columnDynamic = factory(Dynamic::class)->create([
            'type'           => 'column.create',
            'shareable_id'   => $column->id,
            'shareable_type' => 'column',
        ]);

        $this->columnDynamicFlow = factory(Dynamic\Flow::class)->create([
            'author_id'  => $this->columnDynamic->author_id,
            'dynamic_id' => $this->columnDynamic->id,
            'type'       => Dynamic\Flow::TYPE_NORMAL
        ]);

        $this->repostDynamicFlow = factory(Dynamic\Flow::class)->create([
            'author_id'  => $this->user->id,
            'referer_id' => $this->columnDynamicFlow->id,
            'dynamic_id' => $this->columnDynamic->id,
            'type'       => Dynamic\Flow::TYPE_REPOST
        ]);
    }

    public function testShow()
    {
        $id = hashids_encode($this->topicDynamicFlow->id);
        $this->get(sprintf('/dynamic/%s', $id))
            ->seeStatusCode(200)
            ->seeJson([
                'id'       => $id,
                'category' => 'topic.create'
            ]);

        $id = hashids_encode($this->columnDynamicFlow->id);
        $this->get(sprintf('/dynamic/%s', $id))
            ->seeStatusCode(200)
            ->seeJson([
                'id'       => $id,
                'category' => 'column.create'
            ]);

        $id = hashids_encode($this->repostDynamicFlow->id);
        $this->get(sprintf('/dynamic/%s', $id))
            ->seeStatusCode(200)
            ->seeJson([
                'id'       => $id,
                'category' => 'column.create'
            ]);

        $customize = factory(Dynamic::class)->create([
            'type'    => 'default',
            'content' => '自定义动态'
        ]);

        $customize_flow = factory(Dynamic\Flow::class)->create([
            'author_id'  => $this->user->id,
            'dynamic_id' => $customize->id,
            'type'       => Dynamic\Flow::TYPE_NORMAL
        ]);

        $id = hashids_encode($customize_flow->id);
        $this->get(sprintf('/dynamic/%s', $id))
            ->seeStatusCode(200)
            ->seeJson([
                'id'       => $id,
                'category' => 'default'
            ]);

        // 登录测试
        $token = $this->createToken($this->user);

        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        $this->get(sprintf('/dynamic/%s', $id), $headers)
            ->seeStatusCode(200)
            ->seeJson([
                'id'       => $id,
                'category' => 'default'
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

        $id = hashids_encode($this->topicDynamicFlow->id);

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
            'dynamic_id'   => $this->topicDynamicFlow->dynamic_id,
            'referer_id'   => $this->topicDynamicFlow->id,
            'content'      => $expected,
            'repost_count' => 0
        ]);

        $this->seeInDatabase('dynamic_flow', [
            'id'           => $this->topicDynamicFlow->id,
            'repost_count' => $this->topicDynamicFlow->repost_count + 1
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

        $id = hashids_encode($this->topicDynamicFlow->id);

        $params = [
            'comment' => '测试转发内容'
        ];

        $this->post(sprintf('/dynamic/%s/forks', $id), $params, $headers);

        $this->seeStatusCode(205);
        $this->seeInDatabase('dynamic_flow', [
            'type'         => Dynamic\Flow::TYPE_REPOST,
            'author_id'    => $this->user->id,
            'dynamic_id'   => $this->topicDynamicFlow->dynamic_id,
            'referer_id'   => $this->topicDynamicFlow->id,
            'content'      => '测试转发内容',
            'repost_count' => 0
        ]);

        $this->seeInDatabase('dynamic_flow', [
            'id'           => $this->topicDynamicFlow->id,
            'repost_count' => $this->topicDynamicFlow->repost_count + 1
        ]);

        $id = Dynamic\Flow::where('referer_id', $this->topicDynamicFlow->id)->value('id');

        // 再次转发
        $this->post(sprintf('/dynamic/%s/forks', hashids_encode($id)), [
            'comment' => '测试二级转发内容'
        ], $headers);

        $this->seeInDatabase('dynamic_flow', [
            'type'         => Dynamic\Flow::TYPE_REPOST,
            'author_id'    => $this->user->id,
            'dynamic_id'   => $this->topicDynamicFlow->dynamic_id,
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

        Dynamic::where('id', $this->topicDynamic->id)->update([
            'state' => Dynamic::STATE_REMOVE
        ]);

        $this->post(sprintf('/dynamic/%s/forks', hashids_encode($this->topicDynamicFlow->id)), [
            'comment' => '测试已被删除的动态'
        ], $headers)->seeStatusCode(404);

        Dynamic::where('id', $this->topicDynamic->id)->update([
            'state' => Dynamic::STATE_NORMAL
        ]);

        Dynamic\Flow::where('id', $this->topicDynamicFlow->id)->update([
            'state' => Dynamic\Flow::STATE_REMOVE
        ]);

        $this->post(sprintf('/dynamic/%s/forks', hashids_encode($this->topicDynamicFlow->id)), [
            'comment' => '测试已被删除的动态'
        ], $headers)->seeStatusCode(404);

        Dynamic\Flow::where('id', $this->topicDynamicFlow->id)->update([
            'state' => Dynamic\Flow::STATE_LOCKED
        ]);

        $this->post(sprintf('/dynamic/%s/forks', hashids_encode($this->topicDynamicFlow->id)), [
            'comment' => '测试已被锁定的动态'
        ], $headers)->seeStatusCode(403);

        Dynamic::where('id', $this->topicDynamic->id)->update([
            'state' => Dynamic::STATE_LOCKED
        ]);

        Dynamic\Flow::where('id', $this->topicDynamicFlow->id)->update([
            'state' => Dynamic\Flow::STATE_NORMAL
        ]);

        $this->post(sprintf('/dynamic/%s/forks', hashids_encode($this->topicDynamicFlow->id)), [
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

        $id = hashids_encode($this->topicDynamicFlow->id);

        $this->post(sprintf('/dynamic/%s/fabulous', $id), [
            'type' => $type
        ], $headers);

        $this->seeStatusCode($expected);

        if ($expected != 205) {
            return;
        }

        $this->seeInDatabase('dynamic_fabulous', [
            'user_id' => $this->user->id,
            'flow_id' => $this->topicDynamicFlow->id,
            'type'    => $type
        ]);

        // 尝试重复赞

        $this->post(sprintf('/dynamic/%s/fabulous', $id), [
            'type' => $type
        ], $headers);

        $this->seeStatusCode(409);

        $this->seeInDatabase('dynamic_flow', [
            'id'             => $this->topicDynamicFlow->id,
            'fabulous_count' => $this->topicDynamicFlow->fabulous_count + 1,
            'fabulous_type'  => $this->topicDynamicFlow->fabulous_type | $type,
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
            'flow_id' => $this->topicDynamicFlow->id,
            'type'    => 1
        ]);

        $this->topicDynamicFlow->update([
            'fabulous_count' => 1,
            'fabulous_type'  => 1,
            'fabulous_user'  => $this->user->id
        ]);

        $id = hashids_encode($this->topicDynamicFlow->id);

        $this->seeInDatabase('dynamic_flow', [
            'id'             => $this->topicDynamicFlow->id,
            'fabulous_count' => 1,
            'fabulous_type'  => 1,
            'fabulous_user'  => $this->user->id
        ]);

        $this->delete(sprintf('/dynamic/%s/fabulous', $id), [], $headers);

        $this->seeStatusCode(205);

        $this->seeInDatabase('dynamic_flow', [
            'id'             => $this->topicDynamicFlow->id,
            'fabulous_count' => 0,
            'fabulous_type'  => 0,
            'fabulous_user'  => 0
        ]);

        $this->delete(sprintf('/dynamic/%s/fabulous', $id), [], $headers)
            ->seeStatusCode(404);
    }
}
