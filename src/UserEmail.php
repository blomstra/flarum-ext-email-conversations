<?php

namespace Blomstra\PostByMail;

use Flarum\Database\AbstractModel;
use Flarum\User\User;

class UserEmail extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'users_additional_email';
    
    /**
     * {@inheritdoc}
     */
    protected $dates = ['updated_at', 'created_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
