<?php

/*
 * This file is part of blomstra/post-by-mail.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\PostByMail\Event;

use Blomstra\PostByMail\UserEmail;
use Flarum\User\User;

class AbstractAdditionalEmail
{
    /**
     * @param User      $actor
     * @param UserEmail $email
     * @param array     $data
     */
    public function __construct(public User $actor, public User $user, public UserEmail $additionalEmail, public array $data = [])
    {
    }
}
