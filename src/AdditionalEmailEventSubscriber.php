<?php

/*
 * This file is part of blomstra/post-by-mail.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\EmailConversations;

use Blomstra\EmailConversations\Event\AdditionalEmailCreated;
use Flarum\Http\UrlGenerator;
use Flarum\Locale\Translator;
use Flarum\Mail\Job\SendRawEmailJob;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\AccountActivationMailerTrait;
use Flarum\User\EmailToken;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\Queue;

class AdditionalEmailEventSubscriber
{
    use AccountActivationMailerTrait;

    public function __construct(protected SettingsRepositoryInterface $settings, protected Queue $queue, protected UrlGenerator $url, protected Translator $translator)
    {
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            AdditionalEmailCreated::class,
            [$this, 'handle']
        );
    }

    public function handle(AdditionalEmailCreated $event)
    {
        if ($event->additionalEmail->is_confirmed) {
            return;
        }

        $user = $event->user;

        $token = $this->generateToken($user, $event->additionalEmail->email);
        $data = $this->getEmailData($user, $token);

        $this->sendConfirmationEmail($event->additionalEmail->email, $data);
    }

    /**
     * Get the data that should be made available to email template for additional email addresses.
     *
     * @param User       $user
     * @param EmailToken $token
     *
     * @return array
     */
    protected function getEmailData(User $user, EmailToken $token)
    {
        return [
            'username' => $user->display_name,
            'url'      => $this->url->to('forum')->route('blomstraPostByEmail.multiEmails.confirm', ['token' => $token->token]),
            'forum'    => $this->settings->get('forum_title'),
        ];
    }

    /**
     * @param User  $user
     * @param array $data
     */
    protected function sendConfirmationEmail(string $email, $data)
    {
        $body = $this->translator->trans('blomstra-email-conversations.email.multi-emails.body', $data);
        $subject = $this->translator->trans('blomstra-email-conversations.email.multi-emails.subject');

        $this->queue->push(new SendRawEmailJob($email, $subject, $body));
    }
}
