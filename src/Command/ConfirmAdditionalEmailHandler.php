<?php

namespace Blomstra\PostByMail\Command;

use Blomstra\PostByMail\UserEmailRepository;
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
