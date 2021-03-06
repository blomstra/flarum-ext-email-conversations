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

use Blomstra\EmailConversations\Api\Serializer\AdditionalEmailSerializer;
use Blomstra\EmailConversations\UserEmailRepository;
use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Flarum\Http\UrlGenerator;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListUserAdditionalEmailsController extends AbstractListController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = AdditionalEmailSerializer::class;

    /**
     * {@inheritdoc}
     */
    public $include = ['user'];

    /**
     * @param UrlGenerator $url
     */
    public function __construct(protected UrlGenerator $url, protected UserEmailRepository $repository)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);

        $filters = $this->extractFilter($request);
        $sort = $this->extractSort($request);

        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);

        $results = $this->repository->findAllForUser($actor, $actor);

        // $document->addPaginationLinks(
        //     $this->url->to('api')->route('...'),
        //     $request->getQueryParams(),
        //     $offset,
        //     $limit,
        //     $results->areMoreResults() ? null : 0
        // );

        return $results;
    }
}
