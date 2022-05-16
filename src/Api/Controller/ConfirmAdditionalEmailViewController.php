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

use Flarum\Http\Controller\AbstractHtmlController;
use Flarum\User\EmailToken;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface as Request;

class ConfirmAdditionalEmailViewController extends AbstractHtmlController
{
    /**
     * @var Factory
     */
    protected $view;

    /**
     * @param Factory $view
     */
    public function __construct(Factory $view)
    {
        $this->view = $view;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(Request $request)
    {
        $token = Arr::get($request->getQueryParams(), 'token');

        $token = EmailToken::validOrFail($token);

        return $this->view->make('blomstra-email-conversations::confirm-additional-email')
            ->with('csrfToken', $request->getAttribute('session')->token());
    }
}
