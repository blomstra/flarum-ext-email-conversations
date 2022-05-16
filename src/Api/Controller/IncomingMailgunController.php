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

use Blomstra\EmailConversations\Event\EmailReceived;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\EmptyResponse;
use Mailgun\Mailgun;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class IncomingMailgunController implements RequestHandlerInterface
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @var Mailgun
     */
    protected $mailgun;

    /**
     * @param SettingsRepositoryInterface $settings
     * @param UrlGenerator                $url
     */
    public function __construct(SettingsRepositoryInterface $settings, UrlGenerator $url, Dispatcher $events)
    {
        $this->settings = $settings;
        $this->url = $url;
        $this->events = $events;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @throws Exception
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->mailgun = resolve('blomstra.mailgun');
        
        if (!$this->isValidSignature($request)) {
            return new EmptyResponse(406);
        }

        $this->events->dispatch(new EmailReceived(Arr::get($request->getParsedBody(), 'message-url')));

        return new EmptyResponse(200);
    }

    private function isValidSignature(ServerRequestInterface $request): bool
    {
        $body = $request->getParsedBody();

        $timestamp = Arr::get($body, 'timestamp');
        $token = Arr::get($body, 'token');
        $signature = Arr::get($body, 'signature');

        return $this->mailgun->webhooks()->verifyWebhookSignature($timestamp, $token, $signature);
    }
}
