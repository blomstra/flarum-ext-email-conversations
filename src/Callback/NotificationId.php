<?php

/*
 * This file is part of blomstra/email-conversations.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\EmailConversations\Callback;

use Blomstra\EmailConversations\NotificationIdRepository;
use Flarum\Discussion\Discussion;
use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Post\CommentPost;

class NotificationId
{
    public function __construct(protected NotificationIdRepository $notificationIds)
    {
    }

    public function __invoke(BlueprintInterface $blueprint): ?string
    {
        $subjectContext = $blueprint->getSubject();

        if ($subjectContext instanceof CommentPost || $subjectContext instanceof Discussion) {
            $discussion = ($subjectContext instanceof CommentPost) ? $subjectContext->discussion : $subjectContext;

            return $this->notificationIds->getNotificationIdForDiscussion($discussion);
        }
    }
}
