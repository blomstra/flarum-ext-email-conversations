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
use Blomstra\EmailConversations\Event\AdditionalEmailCreated;
use Blomstra\EmailConversations\UserEmail;
use Blomstra\EmailConversations\UserEmailRepository;
use Blomstra\EmailConversations\UserEmailValidator;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Foundation\ValidationException;
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

    public function __construct(protected UserRepository $users, protected UserEmailValidator $validator, protected UserEmailRepository $repository, protected SettingsRepositoryInterface $settings, protected Dispatcher $events)
    {
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

        $actor->assertCan('haveAdditionalEmail', $user);

        $model = new UserEmail();
        $model->user_id = $user->id;
        $model->email = Arr::get($data, 'attributes.email');
        $model->is_confirmed = false;

        $this->validator->assertValid($model->getAttributes());

        $existingCount = $this->repository->getCountForUser($user, $actor);
        $maxCount = $this->settings->get('blomstra-email-conversations.max-additional-emails-count');

        // Workaround for if the max value has been entered, saved and then deleted. This results in the settings key existing in the database
        // but without a value. The default value as defined in `extend.php` is not returned in this instance. Core bug? Needs discussing...
        // TODO: revist this!
        if (!$maxCount) {
            $maxCount = 5;
        }

        if ($existingCount >= $maxCount) {
            throw new ValidationException(['You may only have a maximum of '.$maxCount.' additional email addresses.']);
        }

        $model->saveOrFail();

        $this->events->dispatch(new AdditionalEmailCreated($actor, $user, $model, $data));

        return $model->load('user');
    }
}
