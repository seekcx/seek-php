<?php

namespace Tests\Auth;

use App\Entities\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * @group AuthCredentials
 */
class CredentialsTest extends \TestCase
{
    use DatabaseTransactions;

    protected $user;

    /**
     * 设置基境
     *
     */
    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'id'       => '123',  // 固定 ID，防止一个测试生成的 Token 另一个测试无法使用
            'name'     => 'test',
            'mobile'   => '13812345678',
            'password' => 'test123'
        ]);

        factory(User\Stat::class)->create([
            'user_id' => $this->user->id
        ]);
    }

    /**
     * 测试登录
     *
     */
    public function testLogin()
    {
        $this->post('/user/credentials', [
            'mobile' => '13812345678',
            'secret' => 'test123'
        ]);

        $this->seeStatusCode(200);

        $content = json_decode($this->response->content(), true);

        $this->assertTrue(array_has($content, 'access_token'));

        return array_get($content, 'access_token');
    }

    /**
     * 登录失败案例
     *
     */
    public function testLoginFailure()
    {
        $response = $this->call('POST', '/user/credentials', [
            'mobile' => '13812345679',
            'secret' => 'test123'
        ]);

        $this->assertEquals(401, $response->status());

        $response = $this->call('POST', '/user/credentials', [
            'mobile' => '13812345678',
            'secret' => 'test12'
        ]);

        $this->assertEquals(401, $response->status());
    }

    public function getUserInfo($token, $expected = true)
    {
        $this->get('/user', [
            'Authorization' => 'Bearer ' . $token
        ]);

        if ($expected) {
            $this->seeJson([
                'id'   => hashids_encode(123),
                'name' => 'test'
            ]);
        } else {
            $this->seeJson([
                'message' => 'invalid session or expired'
            ]);
        }
    }

    /**
     * @param $token
     *
     * @depends testLogin
     */
    public function testRefresh($token)
    {
        $this->put('/user/credentials', [], [
            'Authorization' => 'Bearer ' .$token
        ]);

        $this->seeStatusCode(200);
        $this->seeHeader('authorization');
        $payload = $this->response->headers->get('authorization');

        // 使用旧 Token 获取用户详情
        $this->getUserInfo($token, false);

        list(, $token) = explode(' ', $payload);

        $this->getUserInfo($token);
    }

    /**
     * @param $token
     *
     * @depends testLogin
     */
    public function testDestory($token)
    {
        $this->getUserInfo($token);

        $this->delete('/user/credentials', [], [
            'Authorization' => 'Bearer ' .$token
        ]);

        $this->getUserInfo($token, false);
    }
}