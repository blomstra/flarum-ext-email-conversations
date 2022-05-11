<?php

/*
 * This file is part of blomstra/post-by-mail.
 *
 * Copyright (c) 2022 Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\PostByMail\Provider;

use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Settings\SettingsRepositoryInterface;
use Mailgun\Mailgun;

class MailgunProvider extends AbstractServiceProvider
{
    public function register()
    {
        $this->container->singleton('blomstra.mailgun', function (): Mailgun {
            /** @var SettingsRepositoryInterface $settings */
            $settings = resolve('flarum.settings');

            return Mailgun::create($settings->get('blomstra.post-by-mail.mailgun-private-key'));
        });
    }
}
