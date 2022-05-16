<?php

/*
 * This file is part of blomstra/post-by-mail.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\EmailConversations\Jobs;

use Blomstra\EmailConversations\UserEmail;
use Flarum\Discussion\Command\StartDiscussion;
use Flarum\Discussion\Discussion;
use Flarum\Post\Command\PostReply;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Tags\Tag;
use Flarum\User\User;
use Illuminate\Contracts\Bus\Dispatcher;
use Mailgun\Model\Message\ShowResponse;

class ProcessReceivedEmail extends Job
{
    protected $logger;

    public function handle()
    {
        $this->mailgun = resolve('blomstra.mailgun');

        $this->logger = resolve('log');

        /** @var SettingsRepositoryInterface $settings */
        $settings = resolve('flarum.settings');

        /** @var ShowResponse $message */
        $message = $this->mailgun->messages()->show($this->messageUrl);

        $user = $this->findUser($message->getSender());
        $tag = Tag::where('slug', $settings->get('blomstra-email-conversations.tag-slug'))->first();

        if ($user && $tag) {
            if ($discussion = $this->determineDiscussion($message)) {
                //reply to existing discussion

                $this->logger->info("Replying to discussion $discussion->id");
                $this->replyToDiscussion($message, $user, $discussion);
            } else {
                //start new discussion
                $this->logger->info('Starting new discussion');
                $this->startNewDiscussion($message, $user, $tag);
            }
        } else {
            $this->logger->info('No user or tag found');
        }
    }

    private function findUser(string $email): ?User
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            $additional = UserEmail::where('email', $email)->where('is_confirmed', 1)->first();
            $user = User::find($additional->user_id);
        }

        return $user;
    }

    private function determineDiscussion(ShowResponse $message): ?Discussion
    {
        $content = $message->getBodyPlain();
        $matches = null;

        preg_match('/#(?:\w{10})\?(\d*)#/mi', $content, $matches, PREG_UNMATCHED_AS_NULL);

        return Discussion::find($matches[1]);
    }

    private function getPostContent(ShowResponse $message): string
    {
        //TODO HTML -> markdown conversion
        //TODO extract other recipients from the email and add them as @mentions in the post content

        return $message->getStrippedText();
    }

    private function startNewDiscussion(ShowResponse $message, User $actor, Tag $tag): void
    {
        $data = [
            'attributes' => [
                'title'        => $message->getSubject(),
                'content'      => $this->getPostContent($message),
                'source'       => 'blomstra-email-conversations',
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

        //TODO mark the new discussion as awaiting approval
    }

    private function replyToDiscussion(ShowResponse $message, User $actor, Discussion $discussion): void
    {
        $data = [
            'attributes' => [
                'content'      => $this->getPostContent($message),
                'source'       => 'blomstra-email-conversations',
                'source-data'  => $message->getSender(),
            ],
        ];

        resolve(Dispatcher::class)->dispatch(new PostReply($discussion->id, $actor, $data, '127.0.0.1'));
    }
}
