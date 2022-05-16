<?php

/*
 * This file is part of blomstra/post-by-mail.
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
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var UserEmailRepository
     */
    protected $repository;

    /**
     * @param UrlGenerator $url
     */
    public function __construct(UrlGenerator $url, UserEmailRepository $repository)
    {
        $this->url = $url;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        // See https://docs.flarum.org/extend/api.html#api-endpoints for more information.

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
