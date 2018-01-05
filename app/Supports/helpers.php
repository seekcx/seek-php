<?php

use App\Http\Respond;

if (! function_exists('respond')) {
    /**
     * 获取一个 Respond 实例
     *
     * @return Respond
     */
    function respond()
    {
        return app(Respond::class);
    }
}