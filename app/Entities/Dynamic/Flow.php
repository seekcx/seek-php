<?php

namespace App\Entities\Dynamic;

use App\Entities\User;
use App\Entities\Dynamic;
use Illuminate\Database\Eloquent\Model;

class Flow extends Model
{
    /**
     * 模型对应的数据表名
     *
     * @var string
     */
    protected $table = 'dynamic_flow';

    /**
     * 不允许填充的字段
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * 类型
     *
     */
    const TYPE_NORMAL = 1;
    const TYPE_REPOST = 2;

    /**
     * 动态
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dynamic()
    {
        return $this->belongsTo(Dynamic::class);
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

    /**
     * 来源
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referer()
    {
        return $this->belongsTo(Flow::class, 'referer_id');
    }
}
