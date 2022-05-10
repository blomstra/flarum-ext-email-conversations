<?php

/*
 * This file is part of blomstra/post-by-mail.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

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
