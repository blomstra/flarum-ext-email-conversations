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
use FoF\Upload\Commands\Upload;
use FoF\Upload\File;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use League\HTMLToMarkdown\HtmlConverter;
use Mailgun\Model\Message\ShowResponse;
use Psr\Log\LoggerInterface;

/**
 * Mailgun documentation https://documentation.mailgun.com/en/latest/api-sending.html#retrieving-stored-messages.
 */
class ProcessReceivedEmail extends EmailConversationJob
{
    protected string $sourceId = 'blomstra-email-conversations';

    protected const TRIM_TITLE = ['RE:', 're:', 'Re:', 'FW:', 'fw:', 'Fw:', 'Fwd:', 'fwd:', 'FWD:'];

    protected SettingsRepositoryInterface $settings;

    protected LoggerInterface $logger;

    protected HtmlConverter $converter;

    protected Dispatcher $command;

    public function handle()
    {
        $this->mailgun = resolve('blomstra.mailgun');
        $this->settings = resolve('flarum.settings');
        $this->logger = resolve('log');
        $this->command = resolve(Dispatcher::class);
        $this->converter = resolve(HtmlConverter::class);

        $this->process();
    }

    private function process(): void
    {
        /** @var ShowResponse $message */
        $message = $this->mailgun->messages()->show($this->messageUrl);
        //$this->logger->debug('------------------------------------------------------------------------');
        //$this->logger->debug('Received email from '.$message->getSender());
        $user = $this->findUser($message->getSender());
        //$this->logger->debug("Matched to user $user->id $user->username");

        //$this->logger->debug('Message headers:', $message->getMessageHeaders());

        $tag = Tag::where('slug', $this->settings->get('blomstra-email-conversations.tag-slug'))->first();

        if ($user && $tag) {
            if ($discussion = $this->determineDiscussion($message)) {
                //reply to existing discussion

                //$this->logger->debug("Replying to discussion $discussion->id");
                $this->replyToDiscussion($message, $user, $discussion);
            } else {
                //start new discussion
                //$this->logger->debug('Starting new discussion');
                $this->startNewDiscussion($message, $user, $tag);
            }
        } else {
            //$this->logger->debug('No user or tag found');
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

        //$this->logger->debug("Determine discussion - content \n\n$content\n\n --");
        $matches = null;

        $hashMatched = preg_match('/#(\w{40})#/mi', $content, $matches, PREG_UNMATCHED_AS_NULL);

        $discussion = null;

        if (!$hashMatched || $matches[1] === null) {
            //$this->logger->debug("Determine discussion - no notification id found\n\n --");

            if ($this->settings->get('blomstra-email-conversations.match_subject')) {
                //attempt to match based on subject title and source.
                //$this->logger->debug('Looking for matching discussion title: '.$message->getSubject());

                $title = $this->trimTitle($message->getSubject());

                $discussion = Discussion::query()
                    ->where('discussions.title', 'like', '%'.$title)
                    ->whereIn('discussions.id', function ($query) {
                        $query->select('discussion_id')
                            ->from('posts')
                            ->where('source', $this->sourceId);
                    })
                    ->first();
            }
        } else {
            //$this->logger->debug('Determine discussion - match', $matches);

            $discussion = Discussion::where('notification_id', $matches[1])->first();
        }

//        if ($discussion) {
//            $this->logger->debug("Determine discussion - discussion $discussion->id $discussion->title");
//        } else {
//            if ($matches[1]) {
//                $this->logger->debug("Detected discussion but couldn't find it\n\n --");
//            } else {
//                $this->logger->debug('Tried to match based on title: '.$title.', but found nothing');
//            }
//        }

        return $discussion;
    }

    private function trimTitle(string $title): string
    {
        return trim(Str::replace(self::TRIM_TITLE, '', $title));
    }

    private function getPostContent(ShowResponse $message, bool $reply = false): string
    {
        $htmlContent = $reply ? $message->getStrippedHtml() : $message->getBodyHtml();

        $attachments = $message->getAttachments();
        $contentIdMap = $message->getContentIdMap();

        //$this->logger->debug('HTML content: '.$htmlContent);
        //$this->logger->debug('Attachment info:'.print_r($attachments, true));
        //$this->logger->debug('Content ID map:'.print_r($contentIdMap, true));

        foreach ($attachments as $attachment) {
            //$this->logger->debug('Attachment info:', $attachment);

            $file = $this->mailgun->attachment()->show(Arr::get($attachment, 'url'));

            $statusCode = $file->getStatusCode();
            //$this->logger->debug("Got attachment with status code: $statusCode");

            $body = $file->getBody()->getContents();
            //$this->logger->debug("Got attachment body: ".$body);

            //$this->logger->debug('Attachment: '.print_r($file, true));
        }

        foreach ($contentIdMap as $content) {
            //$this->logger->debug('Content:', $content);
        }

        $markdownContent = $this->converter->convert($htmlContent);

        //$this->logger->debug('Markdown content: '.$markdownContent);

        return $markdownContent;
    }

    private function uploadFile(User $actor, $file): File
    {
        return $this->command->dispatch(new Upload(new Collection($file), $actor));
    }

    private function startNewDiscussion(ShowResponse $message, User $actor, Tag $tag): void
    {
        $discussion = $this->startDiscussionFromSource(
            $this->trimTitle($message->getSubject()),
            $this->getPostContent($message),
            $actor,
            $this->sourceId,
            $message->getSender(),
            $tag,
            $message->getBodyHtml()
        );

        $this->subscribeRecipientsToDiscussion($message, $discussion, $actor);

        $this->markForApproval($discussion);

        $this->subscribeMentionedUsers($discussion->firstPost);
    }

    private function replyToDiscussion(ShowResponse $message, User $actor, Discussion $discussion): void
    {
        $post = $this->replyToDiscussionFromSource(
            $discussion,
            $this->getPostContent($message, true),
            $actor,
            $this->sourceId,
            $message->getSender(),
            $message->getBodyHtml()
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
            //$this->logger->debug("Subscribing mentioned user $user->username");
            $this->subscribe($user, $post->discussion);
        }
    }
}
