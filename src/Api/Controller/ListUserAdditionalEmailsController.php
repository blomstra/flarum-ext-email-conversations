<?php

namespace Blomstra\PostByMail\Api\Controller;

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Flarum\Http\UrlGenerator;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Blomstra\PostByMail\Api\Serializer\AdditionalEmailSerializer;
use Blomstra\PostByMail\UserEmailRepository;

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
