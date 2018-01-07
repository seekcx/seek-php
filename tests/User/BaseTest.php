<?php

namespace Tests\User;

use App\Entities\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class BaseTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * 测试注册功能
     *
     * @param string $name     姓名
     * @param string $mobile   手机号
     * @param string $password 密码
     * @param string $captcha  验证码
     * @param string $expected 预期值
     *
     * @dataProvider registerProvider
     * @return void
     */
    public function testRegister($name, $mobile, $password, $captcha, $expected)
    {
        $response = $this->call('POST', '/user', [
            'name'     => $name,
            'mobile'   => $mobile,
            'password' => $password,
            'captcha'  => $captcha
        ]);

        $this->assertEquals($expected ? 200 : 400, $response->status());

        $content = json_decode($response->content(), true);

        $this->assertEquals($expected, array_has($content, 'token'));

        if ($expected) {
            $this->seeInDatabase('user', [
                'name'   => $name,
                'mobile' => $mobile
            ]);
        }
    }

    /**
     * 注册失败案例
     *
     * @return array
     */
    public function registerProvider()
    {
        $provider = collect([
            '正常'            => ['test', '13812345678', 'test123', '123456', true],
            '姓名格式不正确'   => ['t', '13812345678', 'test123', '123456', false],
            '手机号格式不正确' => ['test', '123', 'test123', '123456', false],
            '密码格式不正确'   => ['test', '13812345678', 'test', '123456', false],
            '验证码格式不正确' => ['test', '13812345678', 'test123', '123', false],
        ]);

        collect([
            '%'
        ])->each(function ($char) use ($provider) {
            $provider->put('姓名不能包含：' . $char, ['test' . $char, '13812345678', 'test123', '123456', false]);
        });

        collect([
            '你'
        ])->each(function ($char) use ($provider) {
            $provider->put('姓名可以包含：' . $char, ['test' . $char, '13812345678', 'test123', '123456', true]);
        });

        return $provider->toArray();
    }

    /**
     * 测试用户信息获取
     *
     */
    public function testShow()
    {
        $user = factory(User::class)->create([
            'name' => 'test'
        ]);

        factory(User\Stat::class)->create([
            'user_id' => $user->id
        ]);

        $id = hashids_encode($user->id);

        $this->json('GET', '/user/' .$id)
            ->seeJson([
                'id'   => $id,
                'name' => 'test'
            ]);
    }


}