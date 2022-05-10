<?php

namespace Blomstra\PostByMail\Command;

class ConfirmAdditionalEmail
{
    /**
     * The additional email confirmation token.
     *
     * @param string $token
     */
    public function __construct(public string $token)
    {
    }
}
