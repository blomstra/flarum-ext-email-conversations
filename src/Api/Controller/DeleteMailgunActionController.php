<?php

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
