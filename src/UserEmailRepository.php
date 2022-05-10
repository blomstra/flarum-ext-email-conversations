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

use Flarum\User\EmailToken;
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

    public function getCountForUser(User $user, User $actor = null): int
    {
        $actor->assertCan('viewAdditionalEmailAddresses', $user);

        return UserEmail::where('user_id', $actor->id)->count();
    }

    public function confirm(EmailToken $token): UserEmail
    {
        $userMail = UserEmail::where('email', $token->email)->where('user_id', $token->user_id)->firstOrFail();
        $userMail->is_confirmed = true;
        $userMail->save();

        $token->delete();

        return $userMail;
    }
}
