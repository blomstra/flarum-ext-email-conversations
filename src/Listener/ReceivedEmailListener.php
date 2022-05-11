<?php

namespace Blomstra\PostByMail\Listener;

use Blomstra\PostByMail\Event\EmailReceived;
use Blomstra\PostByMail\Jobs\Job;
use Blomstra\PostByMail\Jobs\ProcessReceivedEmail;
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

        $queue->pushOn(Job::$onQueue, new ProcessReceivedEmail($event->messageUrl));
    }
}
