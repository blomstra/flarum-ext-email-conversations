# Email Conversations

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/blomstra/email-conversations.svg)](https://packagist.org/packages/blomstra/email-conversations) [![Total Downloads](https://img.shields.io/packagist/dt/blomstra/email-conversations.svg)](https://packagist.org/packages/blomstra/email-conversations)

A [Flarum](http://flarum.org) extension. 

## Installation

Install with composer:

```sh
composer require blomstra/email-conversations:"*"
```

## Updating

```sh
composer update blomstra/email-conversations
php flarum migrate
php flarum cache:clear
```

## Integration with `fof/pretty-mail`

If you are using `fof/pretty-mail`, it is required that you update your templates to include `$notificationId`, somewhere within the visible portion of the mail.

## Links

- [Packagist](https://packagist.org/packages/blomstra/email-conversations)
- [GitHub](https://github.com/blomstra/flarum-ext-email-conversations)
- [Discuss](https://discuss.flarum.org/d/PUT_DISCUSS_SLUG_HERE)
