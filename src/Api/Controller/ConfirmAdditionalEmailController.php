<?php

/*
 * This file is part of blomstra/post-by-mail.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\PostByMail\Api\Controller;

use Blomstra\PostByMail\Command\ConfirmAdditionalEmail;
use Flarum\Http\UrlGenerator;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class ConfirmAdditionalEmailController implements RequestHandlerInterface
{
    public function __construct(protected Dispatcher $bus, protected UrlGenerator $url)
    {
    }

    public function handle(Request $request): ResponseInterface
    {
        $token = Arr::get($request->getQueryParams(), 'token');

        $this->bus->dispatch(new ConfirmAdditionalEmail($token));

        return new RedirectResponse($this->url->to('forum')->base());
    }
}
