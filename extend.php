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

use Blomstra\EmailConversations\Api\Serializer\AdditionalEmailSerializer;
use Blomstra\EmailConversations\Event\EmailReceived;
use Blomstra\EmailConversations\Provider\MailgunProvider;
use Flarum\Api\Controller\ShowUserController;
use Flarum\Api\Serializer\CurrentUserSerializer;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\Extend;
use Flarum\User\User;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Model(User::class))
        ->hasMany('additional_emails', UserEmail::class, 'user_id'),

    (new Extend\ApiController(ShowUserController::class))
        ->addInclude('additional_emails'),

    (new Extend\ApiSerializer(CurrentUserSerializer::class))
        ->relationship('additional_emails', function (CurrentUserSerializer $serializer, User $user) {
            return $serializer->hasMany($user, AdditionalEmailSerializer::class, 'additional_emails');
        }),

    // Not worrying about anything but the current user for now, but this will be required for when we add
    // the ability for Mods, etc to edit other users' email addresses.
    // (new Extend\ApiSerializer(UserSerializer::class))
    //     ->relationship('additional_emails', function (UserSerializer $serializer, User $user) {
    //         if ($serializer->getActor()->can('viewAdditionalEmailAddresses', $user)) {
    //             return $serializer->hasMany($user, AdditionalEmailSerializer::class, 'additional_emails');
    //         }
    //     }),

    (new Extend\Routes('api'))
        ->get('/blomstra-additional-email', 'blomstraPostByEmail.multiEmails.list', Api\Controller\ListUserAdditionalEmailsController::class)
        ->post('/blomstra-additional-email', 'blomstraPostByEmail.multiEmails.create', Api\Controller\CreateUserAdditionalEmailController::class)
        ->post('/blomstra-additional-email/{id}', 'blomstraPostByEmail.multiEmails.update', Api\Controller\UpdateUserAdditionalEmailController::class)
        ->delete('/blomstra-additional-email/{id}', 'blomstraPostByEmail.multiEmails.delete', Api\Controller\DeleteUserAdditionalEmailController::class)
        ->post('/email/receive', 'blomstraPostByMail.incoming.receive', Api\Controller\IncomingMailgunController::class),

    (new Extend\Routes('forum'))
        ->get('/confirm/additional-email/{token}', 'blomstraPostByEmail.multiEmails.confirm', Api\Controller\ConfirmAdditionalEmailViewController::class)
        ->post('/confirm/additional-email/{token}', 'blomstraPostByMail.multiEmails.confirm.submit', Api\Controller\ConfirmAdditionalEmailController::class),

    (new Extend\Settings())
        ->default('blomstra-email-conversations.max-additional-emails-count', 5),

    (new Extend\Event())
        ->subscribe(AdditionalEmailEventSubscriber::class)
        ->listen(EmailReceived::class, Listener\ReceivedEmailListener::class),

    (new Extend\View())
        ->namespace('blomstra-email-conversations', __DIR__.'/views'),

    (new Extend\Csrf())
        ->exemptRoute('blomstraPostByMail.incoming.receive'),

    (new Extend\ServiceProvider())
        ->register(MailgunProvider::class),

];
