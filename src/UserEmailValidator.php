<?php

namespace Blomstra\PostByMail;

use Flarum\Foundation\AbstractValidator;

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
            'unique:UserEmail,email'
        ],
        'user_id' => [
            'required',
            'exists:users,id'
        ],
        'is_confirmed' => [
            'required',
            'boolean'
        ]
    ];
}
