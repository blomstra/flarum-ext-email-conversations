<?php

/*
 * This file is part of blomstra/email-conversations.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\EmailConversations;

use Flarum\Discussion\Discussion;
use Flarum\Notification\MailableInterface;
use Flarum\Notification\NotificationMailer;
use Flarum\Post\CommentPost;
use Flarum\User\User;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Mail\Message;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationMailerWithId extends NotificationMailer
{
    public function __construct(Mailer $mailer, TranslatorInterface $translator, protected ViewFactory $view, protected NotificationIdRepository $notificationIds)
    {
        parent::__construct($mailer, $translator);
    }

    /**
     * @param MailableInterface $blueprint
     * @param User              $user
     */
    public function send(MailableInterface $blueprint, User $user)
    {
        $view = $this->addIdToContent($blueprint, $user);

        $this->mailer->raw(
            $view,
            function (Message $message) use ($blueprint, $user) {
                $message->to($user->email, $user->display_name)
                    ->subject($blueprint->getEmailSubject($this->translator));
            }
        );
    }

    private function addIdToContent(MailableInterface $blueprint, User $user): string
    {
        $viewName = $blueprint->getEmailView()['text'] ?? null;

        $subjectContext = $blueprint->getSubject();

        $body = $this->view->make($viewName, compact('blueprint', 'user'))->render();

        if ($subjectContext instanceof CommentPost || $subjectContext instanceof Discussion) {
            $discussion = ($subjectContext instanceof CommentPost) ? $subjectContext->discussion : $subjectContext;
            $data = [
                'user'           => $user,
                'blueprint'      => $blueprint,
                'notificationId' => $this->notificationIds->getNotificationIdForDiscussion($discussion),
            ];

            return BladeCompiler::render('{!! $body !!}'."\n\n".'Notification ID: {!! $notificationId !!}'."\n", array_merge($data, [
                'body' => $body,
            ]));
        }

        return $body;
    }
}
