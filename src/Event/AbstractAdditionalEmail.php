<?php

namespace Blomstra\PostByMail\Event;

use Blomstra\PostByMail\UserEmail;
use Flarum\User\User;

class AbstractAdditionalEmail
{
    /**
     * @param User $actor
     * @param UserEmail $email
     * @param array $data
     */
    public function __construct(public User $actor, public UserEmail $email, public array $data = [])
    {
    }
}
