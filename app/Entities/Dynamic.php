<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Dynamic extends Model
{
    /**
     * 模型对应的数据表名
     *
     * @var string
     */
    protected $table = 'dynamic';

    /**
     * 不允许填充的字段
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * 可分享模型
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function shareable()
    {
        return $this->morphTo('shareable');
    }
}