<?php

/*
 * This file is part of blomstra/email-conversations.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\EmailConversations\Api\Serializer;

use Flarum\Api\Serializer\MailSettingsSerializer;
use Illuminate\Support\Arr;

class EnhancedMailSettingsSerializer extends MailSettingsSerializer
{
    protected function getDefaultAttributes($settings)
    {
        $parentData = parent::getDefaultAttributes($settings);

        return array_merge($parentData, Arr::has($settings, 'route') ?
            [
                'route' => $settings['route'],
            ] : []);
    }
}
