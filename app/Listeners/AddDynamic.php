<?php

namespace App\Listeners;

use Log;
use App\Events\DynamicEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddDynamic implements ShouldQueue
{
    /**
     * 队列名
     *
     * @var string|null
     */
    public $queue = 'dynamic';

    /**
     * 处理
     *
     * @param DynamicEvent $event
     */
    public function handle(DynamicEvent $event)
    {
        $model = $event->model();

        if (!$model) {
            Log::info('丢失可分享模型', [
                'id'      => $event->id,
                'type'    => $event->type(),
                'context' => $event->context()
            ]);

            return;
        }

        $model->dynamic()->create([
            'author_id'  => $event->authorId(),
            'context'    => json_encode($event->context(), JSON_UNESCAPED_UNICODE),
            'created_ip' => $event->ip(),
            'updated_ip' => $event->ip()
        ]);
    }
}