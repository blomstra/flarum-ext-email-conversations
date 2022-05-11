<?php

namespace Blomstra\PostByMail\Event;

class EmailReceived
{
    public function __construct(public ?string $messageUrl)
    {
    }
}
