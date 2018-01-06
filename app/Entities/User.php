<?php

namespace App\Entities;

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
     * 允许填充的字段
     *
     * @var array
     */
    protected $fillable = [
        'name', 'mobile', 'password', 'email', 'avatar', 'gender', 'birthday', 'summary',
        'introduction', 'region_id', 'state'
    ];

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
}