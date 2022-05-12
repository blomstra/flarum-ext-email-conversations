<?php

/*
 * This file is part of blomstra/post-by-mail.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\PostByMail\Jobs;

use Blomstra\PostByMail\UserEmail;
use Flarum\Discussion\Command\StartDiscussion;
use Flarum\Discussion\Discussion;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Tags\Tag;
use Flarum\User\User;
use Illuminate\Contracts\Bus\Dispatcher;
use Mailgun\Model\Message\ShowResponse;

class ProcessReceivedEmail extends Job
{
    public function handle()
    {
        $this->mailgun = resolve('blomstra.mailgun');

        /** @var SettingsRepositoryInterface $settings */
        $settings = resolve('flarum.settings');

        /** @var ShowResponse $message */
        $message = $this->mailgun->messages()->show($this->messageUrl);

        $from = $message->getSender();

        $user = $this->findUser($from);
        $tag = Tag::where('slug', $settings->get('blomstra-post-by-mail.tag-slug'))->first();

        if ($user && $tag) {
            //TODO inspect the message and decide if this is a new discussion or reply to existing

            $this->startNewDiscussion($message, $user, $tag);
        }
    }

    private function findUser(string $email): ?User
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            $additional = UserEmail::where('email', $email)->first();
            $user = User::find($additional->user_id);
        }

        return $user;
    }

    private function startNewDiscussion(ShowResponse $message, User $actor, Tag $tag): void
    {
        $data = [
            'attributes' => [
                'title'   => $message->getSubject(),
                'content' => $message->getStrippedText(),
                'source' => 'blomstra-post-by-mail',
                'source-data'  => $message->getSender(),
            ],
            'relationships' => [
                'tags' => [
                    'data' => [
                        [
                            'id'   => $tag->id,
                            'type' => 'tags',
                        ],
                    ],
                ],
            ],
        ];

        /** @var Discussion $discussion */
        $discussion = resolve(Dispatcher::class)->dispatch(new StartDiscussion($actor, $data, '127.0.0.1'));

        //TODO subscribe all email recipients to the discussion
    }
}
