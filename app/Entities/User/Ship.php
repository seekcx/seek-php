<?php

namespace App\Entities\User;

use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'user_ship';

    /**
     * 不允许填充的字段
     *
     * @var array
     */
    protected $guarded = ['id'];
}
