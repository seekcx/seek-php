<?php

namespace App\Entities\Dynamic;

use App\Entities\User;
use App\Entities\Dynamic;
use Illuminate\Database\Eloquent\Model;

class Fabulous extends Model
{
    /**
     * 模型对应的数据表名
     *
     * @var string
     */
    protected $table = 'dynamic_fabulous';

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
    const TYPE_PRAISE = 1;
    const TYPE_LIKE = 2;
    const TYPE_SMILE = 4;

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
     * 用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
