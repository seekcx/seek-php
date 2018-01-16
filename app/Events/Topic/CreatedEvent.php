<?php

namespace App\Events\Topic;

use App\Events\Event;
use App\Entities\Topic;
use App\Events\DynamicEvent;

class CreatedEvent extends Event implements DynamicEvent
{
    /**
     * 话题ID
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
        return Topic::find($this->id);
    }

    /**
     * 类型
     *
     * @return string
     */
    public function type()
    {
        return 'topic.create';
    }

    /**
     * 话题 ID
     *
     * @return integer
     */
    public function shareableId()
    {
        return $this->id;
    }

    /**
     * 类型
     *
     * @return string
     */
    public function shareableType()
    {
        return 'topic';
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