<?php

namespace App\Repositories;

use DB;
use App\Entities\User;
use App\Entities\Topic;
use Illuminate\Support\Facades\Cache;
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
            abort(400, '你已经关注 ta 啦');
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
            abort(400, '你没有关注 ta');
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

    /**
     * 关注的话题 ID
     *
     * @param $user_id
     *
     * @return array
     */
    public function topics($user_id)
    {
        $cached = Cache::tags(['follow', 'topic']);

        if ($cached->has($user_id)) {
            $this->refreshTopics($user_id);
        }

        return $cached->get($user_id);
    }

    /**
     * 刷新关注的专栏 ID 缓存
     *
     * @param $user_id
     */
    public function refreshTopics($user_id) {
        $lists = DB::table('topic_follower')
            ->where('user_id', $user_id)
            ->pluck('topic_id')
            ->toArray();

        Cache::tags(['follow', 'topic'])->forever($user_id, $lists);
    }

    /**
     * 订阅的专栏 ID
     *
     * @param $user_id
     *
     * @return array
     */
    public function columns($user_id)
    {
        $cached = Cache::tags(['subscribe', 'column']);

        if ($cached->has($user_id)) {
            $this->refreshColumns($user_id);
        }

        return $cached->get($user_id);
    }

    /**
     * 刷新关注的专栏 ID 缓存
     *
     * @param $user_id
     */
    public function refreshColumns($user_id) {
        $lists = DB::table('column_subscriber')
            ->where('user_id', $user_id)
            ->pluck('column_id')
            ->toArray();

        Cache::tags(['subscribe', 'column'])->forever($user_id, $lists);
    }

    /**
     * 关注的话用户 ID
     *
     * @param $user_id
     *
     * @return array
     */
    public function followings($user_id)
    {
        $cached = Cache::tags(['follow', 'user']);

        if (!$cached->has($user_id)) {
            $this->refreshFollowings($user_id);
        }

        return $cached->get($user_id);
    }

    /**
     * 刷新关注的用户 ID 缓存
     *
     * @param $user_id
     */
    public function refreshFollowings($user_id) {
        $lists = User\Ship::where('follower_id', $user_id)
            ->pluck('user_id')
            ->toArray();

        Cache::tags(['follow', 'user'])->forever($user_id, $lists);
    }
}
