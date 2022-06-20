# Email Conversations

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/blomstra/email-conversations.svg)](https://packagist.org/packages/blomstra/email-conversations) [![Total Downloads](https://img.shields.io/packagist/dt/blomstra/email-conversations.svg)](https://packagist.org/packages/blomstra/email-conversations)

A [Flarum](http://flarum.org) extension. 

##### Features
- Allow users to register (and confirm) additional email addresses that can be associated with their account
- Start new discussions via email
- Reply to notification emails
- HTML formatted emails are converted to Markdown and formatted according to the current `TextFormatter` configuration
- Specify a Tag for new discussions started by email
- Option to have new discussions started via email automatically flagged for moderator approval/tag re-assignment, etc
- Option to auto subscribe to discussions participated in via email

##### What do I need to know?

At present, you must be using [Mailgun](https://www.mailgun.com/) to send your email notifications (using the built in `Mailgun` provider). Support for other services _might_ be added in the future, but for now this will be the only provider option.

If you're also using `fof/pretty-mail`, this is supported, but you are required to make minor updates to your templates.

Your email sending domain DNS must have the correct `MX` records [configured for Mailgun](https://documentation.mailgun.com/en/latest/quickstart-receiving.html#how-to-start-receiving-inbound-email) to be able to receive inbound mail. Don't worry about configuring routes, etc, as the extension will configure this for you.

## Installation

Install with composer:

```sh
composer require blomstra/email-conversations:"*"
```

`blomstra/conversations` and `blomstra/email-conversations` will be installed together.

- Enable `Conversations` first, and review the permissions to see the post source. By default this is set to admin and mod groups.
- Enable `Email Conversations`.
- Visit `{YOUR_FORUM}/admin#/mail` and click `Create Mailgun incoming route`. 
- Configure the extension settings as per your requirements `{YOUR_FORUM}/admin#/extension/blomstra-email-conversations`.
- If you're using `fof/pretty-mail`: In each of your HTML templates, add the variable `$notificationId` somewhere in the content. Somewhere in the `<footer>` element is recommended.
- Send a test email to your forum (The email address is the one configured in your email settings tab). The subject line will become the discussion title, the email body will become the post content.

## Updating

```sh
composer update blomstra/email-conversations
php flarum migrate
php flarum cache:clear
```

##### Screenshots

Admin panel
![Admin panel settings](https://x03fddb82-732e-4cdc-8d46-7ff0627bfc91-cdn.blomstra.community/files/2022-06-20/1655724584-946931-image.png)

Post header
![Post header](https://x03fddb82-732e-4cdc-8d46-7ff0627bfc91-cdn.blomstra.community/files/2022-06-20/1655724634-563198-image.png)

Post header for moderators
![Post header for moderators](https://x03fddb82-732e-4cdc-8d46-7ff0627bfc91-cdn.blomstra.community/files/2022-06-20/1655724755-474012-image.png)

Additional email addresses in user settings
![Additional email addresses in user settings](https://x03fddb82-732e-4cdc-8d46-7ff0627bfc91-cdn.blomstra.community/files/2022-06-20/1655724817-954100-image.png)

## Integration with `fof/pretty-mail`

If you are using `fof/pretty-mail`, it is required that you update your templates to include `$notificationId`, somewhere within the visible portion of the mail.

## Links

- [Packagist](https://packagist.org/packages/blomstra/email-conversations)
- [GitHub](https://github.com/blomstra/flarum-ext-email-conversations)
- [Discuss](https://discuss.flarum.org/d/PUT_DISCUSS_SLUG_HERE)
