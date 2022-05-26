<?php

namespace Blomstra\EmailConversations;

use Flarum\Discussion\Discussion;
use Illuminate\Support\Str;

class NotificationIdRepository
{
    /**
     * To prevent cross-posting by manipulating the Notification ID, we generate a 40 character string and
     * store in on the discussion table. This is used by the email processor to identify the discussion, rather
     * than using the discussion ID.
     *
     * @param Discussion $discussion
     *
     * @return string
     */
    public function getNotificationIdForDiscussion(Discussion $discussion): string
    {
        if (!$discussion->notification_id) {
            $discussion->notification_id = Str::random(40);
            $discussion->save();
        }

        return "#$discussion->notification_id#";
    }
}
