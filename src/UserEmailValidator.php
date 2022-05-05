<?php

/*
 * This file is part of blomstra/post-by-mail.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\PostByMail;

use Flarum\Foundation\AbstractValidator;
use Flarum\User\User;

class UserEmailValidator extends AbstractValidator
{
    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'email' => [
            'required',
            'email',
            'max:255',
            'unique:'.UserEmail::class.',email',
            'unique:'.User::class.',email',
        ],
        'user_id' => [
            'required',
            'exists:users,id',
        ],
        'is_confirmed' => [
            'required',
            'boolean',
        ],
    ];
}
