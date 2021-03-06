<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    /**
     * 模型对应的数据表名
     *
     * @var string
     */
    protected $table = 'topic';

    /**
     * 不允许填充的字段
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * 话题创建者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function founder()
    {
        return $this->belongsTo(User::class, 'founder_id');
    }

    /**
     * 关注的用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'topic_follower')
            ->withTimestamps();
    }

    /**
     * 可用
     *
     * @param $query \Illuminate\Database\Eloquent\Builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        return $query->where('state', 1);
    }

    /**
     * 动态
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function dynamic()
    {
        return $this->morphMany(Dynamic::class, 'shareable');
    }
}
