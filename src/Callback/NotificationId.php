<?php

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
