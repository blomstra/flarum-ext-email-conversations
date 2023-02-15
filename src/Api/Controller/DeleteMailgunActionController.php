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

use Flarum\Api\Controller\AbstractDeleteController;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Mailgun\Mailgun;
use Psr\Http\Message\ServerRequestInterface;

class DeleteMailgunActionController extends AbstractDeleteController
{
    public function __construct(protected SettingsRepositoryInterface $settings, protected Mailgun $mailgun)
    {
    }

    protected function delete(ServerRequestInterface $request)
    {
        RequestUtil::getActor($request)->assertAdmin();

        $routeId = $this->settings->get('blomstra-email-conversations.mailgun-route-id');

        return $this->mailgun->routes()->delete($routeId);
    }
}
