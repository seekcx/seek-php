<?php

namespace Tests\Auth;

use App\Entities\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CredentialsTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * 设置基境
     *
     */
    public function setUp()
    {
        parent::setUp();

        factory(User::class)->create([
            'name'     => 'test',
            'mobile'   => '13812345678',
            'password' => 'test123'
        ]);
    }

    /**
     * 测试登录
     *
     */
    public function testLogin()
    {
        $response = $this->call('POST', '/user/credentials', [
            'mobile' => '13812345678',
            'secret' => 'test123'
        ]);

        $this->assertEquals(200, $response->status());

        $content = json_decode($response->content(), true);

        $this->assertTrue(array_has($content, 'token'));
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
}