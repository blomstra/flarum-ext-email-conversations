<?php

namespace Blomstra\PostByMail;

use Flarum\Api\Serializer\PostSerializer;
use Flarum\Post\Post;

class AddPostAttributes
{
    public function __invoke(PostSerializer $serializer, Post $post, array $attributes): array
    {
        if ($serializer->getActor()->can('viewIpsPosts', $post->discussion)) {
            $attributes['source'] = $post->source;
        }

        return $attributes;
    }
}
