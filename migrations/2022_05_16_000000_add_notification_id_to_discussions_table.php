<?php

/*
 * This file is part of blomstra/email-conversations.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use Flarum\Database\Migration;

return Migration::addColumns(
    'discussions',
    [
        'notification_id' => ['text', 'nullable' => true],
    ]
);
