<?php

/*
 * This file is part of blomstra/email-conversations.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\EmailConversations;

use Flarum\Database\AbstractModel;
use Flarum\User\User;

class UserEmail extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'users_additional_email';

    /**
     * {@inheritdoc}
     */
    protected $dates = ['updated_at', 'created_at'];

    /**
     * {@inheritdoc}
     */
    public $timestamps = true;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'is_confirmed' => 'bool',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
