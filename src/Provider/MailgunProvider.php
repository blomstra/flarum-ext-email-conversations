<?php

namespace Blomstra\PostByMail\Provider;

use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Settings\SettingsRepositoryInterface;
use Mailgun\Mailgun;

class MailgunProvider extends AbstractServiceProvider
{
    public function register()
    {
        $this->container->singleton('blomstra.mailgun', function(): Mailgun {
            /** @var SettingsRepositoryInterface $settings */
            $settings = resolve('flarum.settings');

            return Mailgun::create($settings->get('blomstra.post-by-mail.mailgun-private-key'));
        });
    }
}
