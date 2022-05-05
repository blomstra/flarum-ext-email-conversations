<?php

namespace Blomstra\PostByMail\Api\Controller;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Blomstra\PostByMail\Api\Serializer\AdditionalEmailSerializer;
use Blomstra\PostByMail\UserEmailRepository;
use Blomstra\PostByMail\UserEmailValidator;

class UpdateUserAdditionalEmailController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = AdditionalEmailSerializer::class;

    /**
     * @var UserEmailRepository
     */
    protected $repository;

    /**
     * @var UserEmailValidator
     */
    protected $validator;

    public function __construct(UserEmailRepository $repository, UserEmailValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        // See https://docs.flarum.org/extend/api.html#api-endpoints for more information.

        $actor = RequestUtil::getActor($request);
        $modelId = Arr::get($request->getQueryParams(), 'id');
        $data = Arr::get($request->getParsedBody(), 'data', []);

        $model = $this->repository->findOrFail($modelId, $actor);

        $actor->assertCan('editAdditionalEmailAddresses', $model->user());

        $attributes = Arr::get($data, 'attributes', []);
        $relationships = Arr::get($data, 'relationships', []);

        if (isset($attributes['email'])) {
            $model->email = $attributes['email'];
        }

        // if (isset($attributes['isConfirmed'])) {
        //     $model->is_confirmed = $attributes['isConfirmed'];
        // }

        if (isset($relationships['user_id'])) {
            $model->email = $attributes['user_id'];
        }

        $this->validator->assertValid($model->getAttributes());

        $model->saveOrFail();

        return $model;
    }
}
