<?php

namespace Blomstra\PostByMail\Api\Controller;

use Flarum\Api\Controller\AbstractDeleteController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Blomstra\PostByMail\Api\Serializer\AdditionalEmailSerializer;
use Blomstra\PostByMail\UserEmailRepository;

class DeleteUserAdditionalEmailController extends AbstractDeleteController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = AdditionalEmailSerializer::class;

    /**
     * @var UserEmailRepository
     */
    protected $repository;

    public function __construct(UserEmailRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function delete(ServerRequestInterface $request)
    {
        // See https://docs.flarum.org/extend/api.html#api-endpoints for more information.

        $modelId = Arr::get($request->getQueryParams(), 'id');
        $actor = RequestUtil::getActor($request);
        // $input = $request->getParsedBody();

        $model = $this->repository->findOrFail($modelId, $actor);

        $actor->assertCan('editAdditionalEmailAddresses', $model->user());

        $model->deleteOrFail();
    }
}
