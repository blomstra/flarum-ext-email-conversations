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
use Blomstra\PostByMail\UserEmailRepository;
use Flarum\Api\Controller\AbstractDeleteController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

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
