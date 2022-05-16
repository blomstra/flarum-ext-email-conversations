<?php

use Flarum\Database\Migration;

return Migration::addColumns(
    'discussions',
    [
        'notification_id' => ['text', 'nullable' => true],
    ]
);
