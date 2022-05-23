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
use Blomstra\EmailConversations\UserEmailValidator;
use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class UpdateUserAdditionalEmailController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = AdditionalEmailSerializer::class;

    public function __construct(protected UserEmailRepository $repository, protected UserEmailValidator $validator)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $modelId = Arr::get($request->getQueryParams(), 'id');
        $data = Arr::get($request->getParsedBody(), 'data', []);

        $model = $this->repository->findOrFail($modelId, $actor);

        if ($model->user_id !== $actor->id) {
            throw new \Exception('Cannot update additional email address for another user.');
        }

        $attributes = Arr::get($data, 'attributes', []);
        $relationships = Arr::get($data, 'relationships', []);

        if (isset($attributes['email'])) {
            $model->email = $attributes['email'];
        }

        // if (isset($attributes['isConfirmed'])) {
        //     $model->is_confirmed = $attributes['isConfirmed'];
        // }

        if (Arr::has($relationships, 'user.id')) {
            $model->email = $attributes['user']['id'];
        }

        $this->validator->assertValid($model->getAttributes());

        $model->saveOrFail();

        return $model;
    }
}
