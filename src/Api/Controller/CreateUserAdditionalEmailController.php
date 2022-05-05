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

use Blomstra\PostByMail\Api\Serializer\AdditionalEmailSerializer;
use Blomstra\PostByMail\UserEmail;
use Blomstra\PostByMail\UserEmailValidator;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use Flarum\User\UserRepository;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class CreateUserAdditionalEmailController extends AbstractCreateController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = AdditionalEmailSerializer::class;

    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * @var UserEmailValidator
     */
    protected $validator;

    public function __construct(UserRepository $users, UserEmailValidator $validator)
    {
        $this->users = $users;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        // See https://docs.flarum.org/extend/api.html#api-endpoints for more information.

        $actor = RequestUtil::getActor($request);
        $data = Arr::get($request->getParsedBody(), 'data', []);

        $userId = Arr::get($data, 'relationships.user');
        $user = $this->users->findOrFail($userId, $actor);

        $actor->assertCan('editAdditionalEmailAddresses', $user);

        $model = new UserEmail([
            'user_id' => $user->id,
            'email'   => Arr::get($data, 'attributes.email'),
        ]);

        $this->validator->assertValid($model->getAttributes());

        $model->saveOrFail();

        return $model;
    }
}
