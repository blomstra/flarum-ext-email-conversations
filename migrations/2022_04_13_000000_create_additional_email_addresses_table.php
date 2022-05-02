<?php

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
