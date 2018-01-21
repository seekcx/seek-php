<?php

namespace App\Listeners;

use Carbon\Carbon;
use DB;
use Log;
use App\Events\DynamicEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Entities\Dynamic\Flow as DynamicFlow;

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
            Log::info('丢失关联模型', [
                'type'           => $event->type(),
                'shareable_id'   => $event->shareableId(),
                'shareable_type' => $event->shareableType(),
                'context'        => $event->context()
            ]);

            return;
        }

        $triggerAt = $event->triggerAt;

        DB::transaction(function () use ($event, $model, $triggerAt) {
            $dynamic = $model->dynamic()->create([
                'author_id'  => $event->authorId(),
                'type'       => $event->type(),
                'created_ip' => $event->ip(),
                'updated_ip' => $event->ip(),
                'created_at' => $triggerAt,
                'updated_at' => $triggerAt,
            ]);

            DynamicFlow::create([
                'author_id'  => $event->authorId(),
                'dynamic_id' => $dynamic->id,
                'type'       => DynamicFlow::TYPE_NORMAL,
                'created_at' => $triggerAt,
                'updated_at' => $triggerAt,
            ]);
        });
    }
}