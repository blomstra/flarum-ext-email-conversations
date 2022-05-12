<?php

/*
 * This file is part of blomstra/post-by-mail.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\PostByMail\Listener;

use Flarum\Post\Event\Saving;
use Illuminate\Support\Arr;

class SavePostSourceToDatabase
{
    public function handle(Saving $event): void
    {
        if ($source = Arr::get($event->data, 'attributes.source')) {
            $event->post->source = $source;
        }

        if ($sourceData = Arr::get($event->data, 'attributes.source-data')) {
            $event->post->source_data = $sourceData;
        }
    }
}
