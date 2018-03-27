<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    /**
     * 模型对应的数据表名
     *
     * @var string
     */
    protected $table = 'article';

    /**
     * 不允许批量赋值的字段
     *
     * @var array
     */
    protected $guarded = ['id'];

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
     * 专栏
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function column()
    {
        return $this->belongsTo(Column::class, 'column_id');
    }
}
