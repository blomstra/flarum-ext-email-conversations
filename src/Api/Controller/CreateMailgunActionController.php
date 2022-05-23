<?php

/*
 * This file is part of blomstra/email-conversations.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\EmailConversations\Api\Controller;

use Blomstra\EmailConversations\Api\Serializer\MailgunRouteSerializer;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Mailgun\Mailgun;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class CreateMailgunActionController extends AbstractCreateController
{
    public $serializer = MailgunRouteSerializer::class;

    public function __construct(protected SettingsRepositoryInterface $settings, protected Mailgun $mailgun, protected UrlGenerator $url)
    {
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        RequestUtil::getActor($request)->assertAdmin();

        $email = $this->settings->get('mail_from');
        $expression = "match_recipient('$email')";

        $callbackUrl = $this->url->to('api')->route('blomstraPostByMail.incoming.receive');
        $actions = ["store(notify=\"$callbackUrl\")"];

        $description = 'Blomstra Email Conversations incoming route';

        $createResponse = $this->mailgun->routes()->create($expression, $actions, $description);

        $this->settings->set('blomstra-email-conversations.mailgun-route-id', $createResponse->getRoute()->getId());

        return $createResponse->getRoute();
    }
}
