<?php

/*
 * This file is part of blomstra/email-conversations.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\EmailConversations\Listener;

use Blomstra\EmailConversations\Event\EmailReceived;
use Blomstra\EmailConversations\Jobs\EmailConversationJob;
use Blomstra\EmailConversations\Jobs\ProcessReceivedEmail;
use Illuminate\Contracts\Queue\Queue;

class ReceivedEmailListener
{
    public function handle(EmailReceived $event): void
    {
        if (empty($event->messageUrl)) {
            return;
        }

        /** @var Queue */
        $queue = resolve('flarum.queue.connection');

        $queue->pushOn(EmailConversationJob::$onQueue, new ProcessReceivedEmail($event->messageUrl));
    }
}
