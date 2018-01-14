<?php

namespace App\Events\Column;

use App\Events\Event;
use App\Entities\Column;
use App\Events\DynamicEvent;

class CreatedEvent extends Event implements DynamicEvent
{
    /**
     * 专栏ID
     *
     * @var integer
     */
    public $id;

    /**
     * 用户 ID
     *
     * @var integer
     */
    public $userId;

    /**
     * 模型
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model()
    {
        return Column::find($this->id);
    }

    /**
     * 专栏 ID
     *
     * @return integer
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * 类型
     *
     * @return string
     */
    public function type()
    {
        return 'column';
    }

    /**
     * 上下文
     *
     * @return array|string
     */
    public function context()
    {
        return '创建了专栏';
    }

    /**
     * 作者ID
     *
     * @return integer
     */
    public function authorId()
    {
        return $this->userId;
    }

    /**
     * IP
     *
     * @return integer
     */
    public function ip()
    {
        return $this->triggerIp;
    }
}