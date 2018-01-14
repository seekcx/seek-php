<?php

namespace App\Supports\Contracts\Events;

use Auth;
use Carbon\Carbon;

trait TriggerTrait
{
    /**
     * 触发的时间
     *
     * @var Carbon
     */
    public $triggerAt;

    /**
     * 触发的 IP
     *
     * @var string
     */
    public $triggerIp;

    /**
     * 触发的用户
     *
     * @var \App\Entities\User
     */
    public $triggerUser;

    /**
     * 触发的 UA
     *
     * @var string
     */
    public $triggerUserAgent;

    /**
     * 构造器
     */
    public function __construct()
    {
        $this->trigger();
    }

    /**
     * 记录触发事件的环境信息
     *
     */
    public function trigger()
    {
        $this->triggerAt        = Carbon::now();
        $this->triggerIp        = app('request')->ip();
        $this->triggerUser      = Auth::getUser();
        $this->triggerUserAgent = app('request')->userAgent();
    }
}