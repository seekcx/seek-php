<?php

namespace App\Entities;

use App\Entities\User\Ship;
use App\Entities\User\Stat;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * 不允许填充的字段
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * 在JSON格式化中隐藏的字段
     *
     * @var array
     */
    protected $hidden = [
        'password', 'mobile', 'email'
    ];

    /**
     * 获取JWT标识符
     *
     * @return integer
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * 获取自定义Claims
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * 密码 Setter
     *
     * @param string $password 密码
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     * 关联用户数据
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stat()
    {
        return $this->hasOne(Stat::class);
    }

    /**
     * 关注的用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'user_ship', 'follower_id');
    }

    /**
     * 关注的话题
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followTopics()
    {
        return $this->belongsToMany(Topic::class, 'topic_follower')
            ->withTimestamps();
    }

    /**
     * 订阅的专栏
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscribeColumns()
    {
        return $this->belongsToMany(Column::class, 'column_subscriber')
            ->withTimestamps();
    }
}