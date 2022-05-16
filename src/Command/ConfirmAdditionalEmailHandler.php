<?php

/*
 * This file is part of blomstra/post-by-mail.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\EmailConversations\Command;

use Blomstra\EmailConversations\UserEmailRepository;
use Flarum\User\EmailToken;

class ConfirmAdditionalEmailHandler
{
    public function __construct(protected UserEmailRepository $userEmail)
    {
    }

    public function handle(ConfirmAdditionalEmail $command)
    {
        /** @var EmailToken $token */
        $token = EmailToken::validOrFail($command->token);

        $this->userEmail->confirm($token);
    }
}
