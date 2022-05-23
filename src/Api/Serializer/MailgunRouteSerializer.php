<?php

namespace Blomstra\EmailConversations\Api\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Mailgun\Model\Route\Route;

class MailgunRouteSerializer extends AbstractSerializer
{
    public $type = 'mailgun-route';

    protected function getDefaultAttributes($route)
    {
        /** @var Route $route */

        $actions = [];

        foreach ($route->getActions() as $action) {
            $actions[] = [
                'action' => $action->getAction(),
            ];
        }
        
        return [
            'id' => $route->getId(),
            'actions' => $actions,
            'description' => $route->getDescription(),
            'filter' => $route->getFilter(),
            'priority' => $route->getPriority(),
            'created_at' => $route->getCreatedAt(),
        ];
    }

    public function getId($route)
    {
        return $route->getId();
    }
}
