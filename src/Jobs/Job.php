<?php

/*
 * This file is part of blomstra/post-by-mail.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\PostByMail\Jobs;

use Flarum\Queue\AbstractJob;
use Mailgun\Mailgun;

class Job extends AbstractJob
{
    public static ?string $onQueue = null;

    public ?Mailgun $mailgun = null;

    public function __construct(protected string $messageUrl)
    {
        if (static::$onQueue) {
            $this->onQueue(static::$onQueue);
        }
    }
}
