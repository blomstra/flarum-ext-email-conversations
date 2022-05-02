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

use Flarum\Api\Serializer\AbstractSerializer;

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
}
