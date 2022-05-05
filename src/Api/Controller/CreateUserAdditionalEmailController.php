<?php

namespace Blomstra\PostByMail\Api\Controller;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Blomstra\PostByMail\Api\Serializer\AdditionalEmailSerializer;
use Blomstra\PostByMail\UserEmail;
use Blomstra\PostByMail\UserEmailValidator;
use Flarum\User\UserRepository;

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
            'email' => Arr::get($data, 'attributes.email'),
        ]);

        $this->validator->assertValid($model->getAttributes());

        $model->saveOrFail();

        return $model;
    }
}
