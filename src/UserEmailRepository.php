<?php

namespace Blomstra\PostByMail;

use Flarum\User\User;
use Illuminate\Database\Eloquent\Builder;
use Blomstra\PostByMail\UserEmail;
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
     * @param int $id
     * @param User $actor
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
