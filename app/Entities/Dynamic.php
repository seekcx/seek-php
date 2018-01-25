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
     * 状态
     *
     */
    const STATE_NORMAL = 1;
    const STATE_REMOVE = 0;
    const STATE_LOCKED = -1;

    /**
     * 可分享模型
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function shareable()
    {
        return $this->morphTo('shareable');
    }

    /**
     * 作者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
