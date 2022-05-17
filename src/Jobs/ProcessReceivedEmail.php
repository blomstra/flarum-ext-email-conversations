<?php

/*
 * This file is part of blomstra/email-conversations.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\EmailConversations\Jobs;

use Blomstra\EmailConversations\UserEmail;
use Flarum\Discussion\Discussion;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Tags\Tag;
use Flarum\User\User;
use Mailgun\Model\Message\ShowResponse;

class ProcessReceivedEmail extends EmailConversationJob
{
    protected string $sourceId = 'blomstra-email-conversations';

    protected SettingsRepositoryInterface $settings;

    protected $logger;

    public function handle()
    {
        $this->mailgun = resolve('blomstra.mailgun');
        $this->settings = resolve('flarum.settings');

        $this->logger = resolve('log');

        /** @var ShowResponse $message */
        $message = $this->mailgun->messages()->show($this->messageUrl);

        $user = $this->findUser($message->getSender());
        $tag = Tag::where('slug', $this->settings->get('blomstra-email-conversations.tag-slug'))->first();

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

        preg_match('/#(\w{40})#/mi', $content, $matches, PREG_UNMATCHED_AS_NULL);

        if ($matches[1] === null) {
            return null;
        }

        return Discussion::where('notification_id', $matches[1])->first();
    }

    private function getPostContent(ShowResponse $message): string
    {
        //TODO HTML -> markdown conversion
        //TODO extract other recipients from the email and add them as @mentions in the post content

        return $message->getStrippedText();
    }

    private function startNewDiscussion(ShowResponse $message, User $actor, Tag $tag): void
    {
        $discussion = $this->startDiscussionFromSource(
            $message->getSubject(),
            $this->getPostContent($message),
            $actor,
            $this->sourceId,
            $message->getSender(),
            $tag
        );

        $this->subscribeRecipientsToDiscussion($message, $discussion, $actor);

        $this->markForApproval($discussion);

        $this->subscribeMentionedUsers($discussion->firstPost);
    }

    private function replyToDiscussion(ShowResponse $message, User $actor, Discussion $discussion): void
    {
        $post = $this->replyToDiscussionFromSource(
            $discussion,
            $this->getPostContent($message),
            $actor,
            $this->sourceId,
            $message->getSender()
        );

        $this->subscribeRecipientsToDiscussion($message, $post->discussion, $actor);

        $this->subscribeMentionedUsers($post);
    }

    private function subscribeRecipientsToDiscussion(ShowResponse $message, Discussion $discussion, User $author): void
    {
        if ((bool) !$this->settings->get('blomstra-email-conversations.auto-subscribe')) {
            return;
        }

        // Subscribe the author to the discussion, if they're not already subscribed.
        $this->subscribe($author, $discussion);

        // TODO Subscribe recipients included in the 'to' and 'cc' fields of the inbound email
        // $this->logger->info(print_r($message, true));
        // $recipients = explode(',', $message->getRecipients());
        // $r = $message->getRecipients();
        // $this->logger->info("Attempting to subscribe: $r");

        // foreach ($recipients as $recipient) {
        //     //TODO - exclude the known forum mailer email address
        //     $this->logger->info("Looking for $recipient");
        //     $user = $this->findUser($recipient);

        //     if ($user) {
        //         $this->subscribe($user, $discussion);
        //     }
        // }
    }

    private function subscribe(User $user, Discussion $discussion): void
    {
        $state = $discussion->stateFor($user);

        if ($state->subscription !== 'follow') {
            $state->subscription = 'follow';
            $state->save();
        }
    }

    private function markForApproval(Discussion $discussion): void
    {
        if ((bool) !$this->settings->get('blomstra-email-conversations.require_approval')) {
            return;
        }

        $discussion->is_approved = false;
        $discussion->firstPost->is_approved = false;
        $discussion->firstPost->save();
        $discussion->save();
    }

    private function subscribeMentionedUsers(Post $post): void
    {
        $users = $post->mentionsUsers;

        foreach ($users as $user) {
            $this->logger->info("Subscribing mentioned user $user->username");
            $this->subscribe($user, $post->discussion);
        }
    }
}
