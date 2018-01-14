<?php

namespace App\Events;

interface DynamicEvent
{
    /**
     * 模型
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model();

    /**
     * 上下文
     *
     * @return array|string
     */
    public function context();

    /**
     * 可分享 ID
     *
     * @return integer
     */
    public function id();

    /**
     * 类型
     *
     * @return string
     */
    public function type();

    /**
     * 作者ID
     *
     * @return integer
     */
    public function authorId();

    /**
     * IP
     *
     * @return integer
     */
    public function ip();
}