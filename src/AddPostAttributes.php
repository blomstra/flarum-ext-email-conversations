<?php

/*
 * This file is part of blomstra/post-by-mail.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\PostByMail;

use Flarum\Api\Serializer\PostSerializer;
use Flarum\Post\Post;

class AddPostAttributes
{
    public function __invoke(PostSerializer $serializer, Post $post, array $attributes): array
    {
        $attributes['source'] = $post->source;
        
        if ($serializer->getActor()->can('viewIpsPosts', $post->discussion)) {
            $attributes['source_data'] = $post->source_data;
        }

        return $attributes;
    }
}
