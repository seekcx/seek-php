<?php

namespace Tests\Dynamic;

use App\Entities\User;
use App\Entities\Topic;
use App\Entities\Dynamic;
use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * @group dynamic.index
 */
class IndexTest extends \TestCase
{
    use DatabaseTransactions;

    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'name'     => '动态测试账号',
            'password' => 'test123'
        ]);
    }

    /**
     * 首页未登录
     *
     */
    public function testNotLogin()
    {
        $topic = factory(Topic::class)->create([
            'name'    => '测试话题',
            'summary' => '测试话题的描述信息'
        ]);

        $dynamic = factory(Dynamic::class)->create([
            'type'           => 'topic.create',
            'shareable_id'   => $topic->id,
            'shareable_type' => 'topic',
        ]);

        $dynamic_flow = factory(Dynamic\Flow::class)->create([
            'author_id'  => $this->user->id,
            'dynamic_id' => $dynamic->id,
            'type'       => Dynamic\Flow::TYPE_NORMAL
        ]);

        $this->get('/dynamics')
            ->seeStatusCode(200)
            ->seeJson([
                'id'       => hashids_encode($dynamic_flow->id),
                'category' => 'topic.create'
            ]);
    }

    /**
     * 首页已登录
     *
     */
    public function testLogined()
    {
        $token = $this->createToken($this->user);

        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        $dynamic = factory(Dynamic::class)->create([
            'author_id' => $this->user->id,
            'type'      => 'default'
        ]);

        $flow = factory(Dynamic\Flow::class)->create([
            'author_id'  => $this->user->id,
            'dynamic_id' => $dynamic->id,
            'type'       => Dynamic\Flow::TYPE_NORMAL
        ]);

        $this->get('/dynamics', $headers)
            ->seeStatusCode(200)
            ->seeJson([
                'id'       => hashids_encode($flow->id),
                'category' => 'default'
            ]);

        // @todo: 覆盖测试（动态逻辑复杂，之后添加业务覆盖测试）
    }
}
