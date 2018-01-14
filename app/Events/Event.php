<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Supports\Contracts\Events\TriggerTrait;

abstract class Event
{
    use SerializesModels, TriggerTrait;
}
