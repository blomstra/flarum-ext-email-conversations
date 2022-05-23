<?php

namespace Blomstra\EmailConversations\Api\Serializer;

use Flarum\Api\Serializer\MailSettingsSerializer;
use Illuminate\Support\Arr;

class EnhancedMailSettingsSerializer extends MailSettingsSerializer
{
    protected function getDefaultAttributes($settings)
    {
        $parentData = parent::getDefaultAttributes($settings);
        //dd($settings);
        return array_merge($parentData, Arr::has($settings, 'route') ?
            [
                'route' => $settings['route']
            ] : [] );
    }
}
