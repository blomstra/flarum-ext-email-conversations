<?php

/*
 * This file is part of blomstra/email-conversations.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\EmailConversations\Jobs;

use Blomstra\Conversations\Job\ConversationJob;
use Mailgun\Mailgun;

class EmailConversationJob extends ConversationJob
{
    public ?Mailgun $mailgun = null;

    public function __construct(protected string $messageUrl)
    {
        parent::__construct();
    }
}
