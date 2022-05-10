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
use Blomstra\PostByMail\Event\AdditionalEmailCreated;
use Blomstra\PostByMail\UserEmail;
use Blomstra\PostByMail\UserEmailRepository;
use Blomstra\PostByMail\UserEmailValidator;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\UserRepository;
use Illuminate\Contracts\Events\Dispatcher;
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

    /**
     * @var UserEmailRepository
     */
    protected $repository;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var Dispatcher
     */
    protected $events;

    public function __construct(UserRepository $users, UserEmailValidator $validator, UserEmailRepository $repository, SettingsRepositoryInterface $settings, Dispatcher $events)
    {
        $this->users = $users;
        $this->validator = $validator;
        $this->repository = $repository;
        $this->settings = $settings;
        $this->events = $events;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $data = Arr::get($request->getParsedBody(), 'data', []);

        $userId = Arr::get($data, 'relationships.user.data.id');

        $user = $this->users->findOrFail($userId, $actor);

        $actor->assertCan('editAdditionalEmailAddresses', $user);

        $model = new UserEmail();
        $model->user_id = $user->id;
        $model->email = Arr::get($data, 'attributes.email');
        $model->is_confirmed = false;

        $this->validator->assertValid($model->getAttributes());

        $existingCount = $this->repository->getCountForUser($user, $actor);
        $maxCount = $this->settings->get('blomstra-post-by-mail.max-additional-emails-count', 5);

        if ($existingCount >= $maxCount) {
            throw new \Flarum\Foundation\ValidationException(['You may only have a maximum of '.$maxCount.' additional email addresses.']);
        }

        $model->saveOrFail();

        $this->events->dispatch(new AdditionalEmailCreated($actor, $model, $data));

        return $model->load('user');
    }
}
