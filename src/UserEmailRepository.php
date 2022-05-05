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

use Flarum\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserEmailRepository
{
    /**
     * @return Builder
     */
    public function query()
    {
        return UserEmail::query();
    }

    /**
     * @param int  $id
     * @param User $actor
     *
     * @return UserEmail
     */
    public function findOrFail($id, User $actor = null): UserEmail
    {
        $email = UserEmail::with(['user'])->findOrFail($id);

        $actor->assertCan('viewAdditionalEmailAddresses', $email->user());

        return $email;
    }

    public function findAllForUser(User $user, User $actor = null): Collection
    {
        $actor->assertCan('viewAdditionalEmailAddresses', $user);

        return UserEmail::with(['user'])->where('user_id', $actor->id)->get();
    }
}
