<?php

namespace App\Repositories;

use DB;
use App\Entities\User;
use App\Repositories\Contracts\UserRepository;
use Prettus\Repository\Eloquent\BaseRepository;

class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * 注册
     *
     * @param string $name 名字
     * @param string $mobile 手机号
     * @param string $password 密码
     *
     * @return User
     */
    public function register($name, $mobile, $password)
    {
        $user = DB::transaction(function () use ($name, $mobile, $password) {
            $user = $this->create([
                'name'     => $name,
                'mobile'   => $mobile,
                'password' => $password
            ]);

            $this->initializeStat($user->id);

            return $user;
        });

        return $user;
    }

    /**
     * 初始化用户统计数据表
     *
     * @param integer $user_id 用户ID
     */
    protected function initializeStat($user_id)
    {
        $now = date('Y-m-d H:i:s');
        $ip  = app('request')->ip();

        User\Stat::create([
            'user_id'        => $user_id,
            'register_at'    => $now,
            'last_login_at'  => $now,
            'last_active_at' => $now,
            'register_ip'    => $ip,
            'last_login_ip'  => $ip,
            'last_active_ip' => $ip
        ]);
    }
}
