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
use Illuminate\Support\Arr;
use League\HTMLToMarkdown\HtmlConverter;
use Mailgun\Model\Message\ShowResponse;
use Psr\Log\LoggerInterface;

class ProcessReceivedEmail extends EmailConversationJob
{
    protected string $sourceId = 'blomstra-email-conversations';

    protected SettingsRepositoryInterface $settings;

    protected LoggerInterface $logger;

    protected HtmlConverter $converter;

    public function handle()
    {
        $this->mailgun = resolve('blomstra.mailgun');
        $this->settings = resolve('flarum.settings');
        $this->logger = resolve('log');
        $this->converter = new HtmlConverter([
            'strip_tags'    => true,
            'use_autolinks' => false,
            'remove_nodes'  => 'style script',
        ]);

        $this->process();
    }

    private function process(): void
    {
        /** @var ShowResponse $message */
        $message = $this->mailgun->messages()->show($this->messageUrl);
        $this->logger->debug("------------------------------------------------------------------------");
        $this->logger->debug("Received email from " . $message->getSender());
        $user = $this->findUser($message->getSender());
        $this->logger->debug("Matched to user $user->id $user->username");

        $this->logger->debug("Message headers:", $message->getMessageHeaders());
        
        $tag = Tag::where('slug', $this->settings->get('blomstra-email-conversations.tag-slug'))->first();

        if ($user && $tag) {
            if ($discussion = $this->determineDiscussion($message)) {
                //reply to existing discussion

                $this->logger->debug("Replying to discussion $discussion->id");
                $this->replyToDiscussion($message, $user, $discussion);
            } else {
                //start new discussion
                $this->logger->debug('Starting new discussion');
                $this->startNewDiscussion($message, $user, $tag);
            }
        } else {
            $this->logger->debug('No user or tag found');
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
        $this->logger->debug("Determine discussion - content \n\n$content\n\n --");
        $matches = null;

        preg_match('/#(\w{40})#/mi', $content, $matches, PREG_UNMATCHED_AS_NULL);

        if ($matches[1] === null) {
            $this->logger->debug("Determine discussion - no notification id found\n\n --");
            return null;
        }

        $this->logger->debug("Determine discussion - match", $matches);

        $discussion = Discussion::where('notification_id', $matches[1])->first();

        if ($discussion) {
            $this->logger->debug("Determine discussion - discussion $discussion->id $discussion->title");
        } else {
            $this->logger->debug("Detected discussion but couldn't find it\n\n --");
        }

        return $discussion;
    }

    private function getPostContent(ShowResponse $message): string
    {
        $htmlContent = $message->getStrippedHtml();
        $attachments = $message->getAttachments();

        //$this->logger->debug('HTML content: '.$htmlContent);
        //$this->logger->debug('Attachment info:'.print_r($attachments, true));

        foreach ($attachments as $attachment) {
            $file = $this->mailgun->attachment()->show(Arr::get($attachment, 'url'));
        }

        return $this->converter->convert($htmlContent);
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
            $this->logger->debug("Subscribing mentioned user $user->username");
            $this->subscribe($user, $post->discussion);
        }
    }
}
