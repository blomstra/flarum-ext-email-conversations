<?php

namespace Blomstra\PostByMail\Listener;

use Flarum\Post\Event\Saving;
use Illuminate\Support\Arr;

class SavePostSourceToDatabase
{
    public function handle(Saving $event): void
    {
        $source = Arr::get($event->data, 'attributes.source');

        if ($source) {
            $event->post->source = $source;
        }
    }
}
