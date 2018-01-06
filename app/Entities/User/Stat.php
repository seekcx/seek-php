<?php

namespace App\Entities\User;

use App\Entities\User;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'user_stat';

    /**
     * 主键字段
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * 指定模型是否需要维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 应被转换为日期的属性
     *
     * @var array
     */
    protected $dates = [
        'register_at',
        'last_login_at',
        'last_active_at'
    ];

    /**
     * 允许填充的字段
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'register_at', 'last_login_at', 'last_active_at', 'register_ip',
        'last_login_ip', 'last_active_ip'
    ];

    /**
     * 自增主键
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * 用户关联
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
