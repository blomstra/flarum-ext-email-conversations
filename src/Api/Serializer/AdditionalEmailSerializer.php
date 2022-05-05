<?php

/*
 * This file is part of blomstra/post-by-mail.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\PostByMail\Api\Serializer;

use Blomstra\PostByMail\UserEmail;
use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;

class AdditionalEmailSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'additionalEmail';

    /**
     * {@inheritdoc}
     */
    protected function getDefaultAttributes($additionalEmail)
    {
        return [
            'id'          => $additionalEmail->id,
            'email'       => $additionalEmail->email,
            'isConfirmed' => $additionalEmail->is_confirmed,
            'createdAt'   => $this->formatDate($additionalEmail->created_at),
            'updatedAt'   => $this->formatDate($additionalEmail->updated_at),
        ];
    }

    /**
     * @return \Tobscure\JsonApi\Relationship
     */
    protected function user(UserEmail $additionalEmail)
    {
        return $this->hasOne($additionalEmail, BasicUserSerializer::class);
    }
}
