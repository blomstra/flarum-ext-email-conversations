<?php

namespace Blomstra\PostByMail\Jobs;

use Flarum\Queue\AbstractJob;
use Mailgun\Mailgun;

class Job extends AbstractJob
{
    public static ?string $onQueue = null;

    public ?Mailgun $mailgun = null;

    public function __construct(protected string $messageUrl)
    {
        if (static::$onQueue) $this->onQueue(static::$onQueue);
    }
}
