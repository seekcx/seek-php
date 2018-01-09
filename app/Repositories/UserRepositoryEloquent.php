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
     * @param string $name     名字
     * @param string $mobile   手机号
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

    /**
     * 关注
     *
     * @param integer $user_id     用户ID
     * @param integer $follower_id 粉丝ID
     */
    public function follow($user_id, $follower_id)
    {
        if ($user_id == $follower_id) {
            abort(400, '不能关注自己');
        }

        $is_follow = User\Ship::where('user_id', $user_id)
            ->where('follower_id', $follower_id)
            ->exists();

        if ($is_follow) {
            abort(409, '你已经关注 ta 啦');
        }

        $ship = User\Ship::where('user_id', $follower_id)
            ->where('follower_id', $user_id)
            ->first();

        DB::transaction(function () use ($user_id, $follower_id, $ship) {
            User\Ship::create([
                'user_id'     => $user_id,
                'follower_id' => $follower_id,
                'cross'       => $ship ? 1 : 0
            ]);

            if ($ship) {
                $ship->cross = 1;
                $ship->save();
            }

            User\Stat::where('user_id', $user_id)->increment('followers');
            User\Stat::where('user_id', $follower_id)->increment('following');
        });
    }

    /**
     * 取消关注
     *
     * @param integer $user_id     用户ID
     * @param integer $follower_id 粉丝ID
     */
    public function unfollow($user_id, $follower_id)
    {
        if ($user_id == $follower_id) {
            abort(400, '不能取关自己');
        }

        $ship = User\Ship::where('user_id', $user_id)
            ->where('follower_id', $follower_id)
            ->first();

        if (!$ship) {
            abort(409, '你没有关注 ta');
        }

        DB::transaction(function () use ($user_id, $follower_id, $ship) {
            $ship->delete();

            if ($ship->cross) {
                User\Ship::where('user_id', $follower_id)
                    ->where('follower_id', $user_id)
                    ->update([
                        'cross' => 0
                    ]);
            }

            User\Stat::where('user_id', $user_id)->decrement('followers');
            User\Stat::where('user_id', $follower_id)->decrement('following');
        });
    }

}
