<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    /**
     * 模型对应的数据表名
     *
     * @var string
     */
    protected $table = 'column';

    /**
     * 不允许批量赋值的字段
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * 角色
     *
     * @var integer
     */
    const ROLE_CONTRIBUTORS = 1;       // 投稿
    const ROLE_EDITOR = 2;             // 编辑
    const ROLE_MANAGER = 4;            // 内容管理
    const ROLE_OWNER = 8;              // 所有者

    /**
     * 成员
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'column_member', 'column_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * 话题
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function topics()
    {
        return $this->belongsToMany(Topic::class)
            ->withTimestamps();
    }

    /**
     * 所有者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * 关注者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscriber()
    {
        return $this->belongsToMany(User::class, 'column_subscriber')
            ->withTimestamps();
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
