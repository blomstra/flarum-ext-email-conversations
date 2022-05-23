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

use Blomstra\EmailConversations\Api\Serializer\EnhancedMailSettingsSerializer;
use Flarum\Api\Controller\ShowMailSettingsController;
use Flarum\Settings\SettingsRepositoryInterface;
use Mailgun\Exception\HttpClientException;
use Mailgun\Mailgun;
use Mailgun\Model\Route\Route;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ShowEnhancedMailSettingsController extends ShowMailSettingsController
{
    public $serializer = EnhancedMailSettingsSerializer::class;

    public function __construct(protected Mailgun $mailgun, protected SettingsRepositoryInterface $settings)
    {
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        // Unless the mail driver is mailgun, we don't need to do anything here.
        if ($this->settings->get('mail_driver') !== 'mailgun') {
            return parent::data($request, $document);
        }

        $errors = [];
        if (empty($this->settings->get('blomstra-email-conversations.mailgun-route-id'))) {
            $errors[] = ['blomstra' => ['mailgun-route-id' => 'The mailgun route id is not set.']];

            return array_merge(parent::data($request, $document), ['errors' => $errors]);
        }

        try {
            /** @var Route $route */
            $response = $this->mailgun->routes()->show($this->settings->get('blomstra-email-conversations.mailgun-route-id'));
            $route = $response->getRoute();

            $actions = [];

            foreach ($route->getActions() as $action) {
                $actions[] = [
                    'action' => $action->getAction(),
                ];
            }

            $route = [
                'id'          => $route->getId(),
                'actions'     => $actions,
                'description' => $route->getDescription(),
                'filter'      => $route->getFilter(),
                'priority'    => $route->getPriority(),
                'created_at'  => $route->getCreatedAt(),
            ];

            return array_merge(parent::data($request, $document), ['route' => $route]);
        } catch (HttpClientException $e) {
            return parent::data($request, $document);
        }
    }
}
