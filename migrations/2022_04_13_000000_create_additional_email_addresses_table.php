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
use Illuminate\Database\Schema\Blueprint;

return Migration::createTable(
    'users_additional_email',
    function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->unsigned();
        $table->string('email')->unique();
        $table->boolean('is_confirmed')->default(false);
        $table->timestamps();
    }
);
