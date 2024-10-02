<?php

namespace Actengage\CaseyJones\Listeners;

use Actengage\CaseyJones\Events\SendCreated;
use Actengage\CaseyJones\Models\Send;
use Illuminate\Support\Arr;

class SendWasCreated
{
    /**
     * Handle the given event.
     */
    public function handle(SendCreated $event): void
    {
        $model = Send::make()->forceFill(Arr::except($event->send, 'app'));
        $model->save();

        dd($model);
    }
}