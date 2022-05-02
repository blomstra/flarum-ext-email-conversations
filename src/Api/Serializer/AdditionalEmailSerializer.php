<?php

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
            'id' => $additionalEmail->id,
            'email' => $additionalEmail->email,
            'isConfirmed' => $additionalEmail->is_confirmed,
            'createdAt' => $this->formatDate($additionalEmail->created_at),
            'updatedAt' => $this->formatDate($additionalEmail->updated_at),
        ];
    }
}
