---
sidebar_position: 5
title: Laravel
---

Use the package in Laravel via the built-in service provider.

## Service provider registration

`ApiClientServiceProvider` is auto-discovered by Laravel through Composer (`extra.laravel.providers`), so manual registration is not required in a standard setup.

If you need manual registration, add:

```php
TheMarketer\ApiClient\Laravel\ApiClientServiceProvider::class
```

to your app providers list.

## Environment variables

Set credentials and optional overrides in `.env`:

```dotenv
THEMARKETER_CUSTOMER_ID=YOUR_CUSTOMER_ID
THEMARKETER_REST_KEY=YOUR_REST_KEY
THEMARKETER_TRACKING_KEY=YOUR_TRACKING_KEY

# Optional
THEMARKETER_REST_URL=https://t.themarketer.com
THEMARKETER_TRACKING_URL=https://t.themarketer.com
THEMARKETER_MAX_RETRY_ATTEMPTS=1
```

## Publish package config (optional)

The provider supports publishing config:

```bash
php artisan vendor:publish --tag=themarketer-api-client-config
```

This creates `config/themarketer-api-client.php`.

## Resolve the client from the container

The provider registers a singleton for `TheMarketer\ApiClient\Client`.

```php
use TheMarketer\ApiClient\Client;

$client = app(Client::class);
$ordersApi = $client->orders();
```

An alias is also registered:

```php
$client = app('themarketer.api-client');
```

## Use the facade

The package also registers a Laravel facade alias: `TheMarketer`.

```php
use TheMarketer\ApiClient\Laravel\Facades\TheMarketer;

$ordersApi = TheMarketer::orders();
$config = TheMarketer::config();
```

## Laravel Mail transport

The provider registers a custom mail transport named `themarketer`, backed by `TheMarketerTransport`.
It sends emails through `transactionals()` from this package.

Add a mailer entry in `config/mail.php`:

```php
'mailers' => [
    // ...
    'themarketer' => [
        'transport' => 'themarketer',
        // Optional fallback sender/reply-to used when the message does not set them
        'from' => ['address' => env('MAIL_FROM_ADDRESS')],
        'reply_to' => ['address' => env('MAIL_REPLY_TO_ADDRESS')],
    ],
],
```

You can set it as default:

```env
MAIL_MAILER=themarketer
```

Or call it explicitly:

```php
use Illuminate\Support\Facades\Mail;

Mail::mailer('themarketer')->raw('Hello from Laravel', function ($message) {
    $message->to('user@example.com')->subject('Welcome');
});
```

### Transport behavior

- Supports Symfony/Laravel `Email` messages.
- Collects recipients from `to`, `cc`, and `bcc` (unique addresses).
- Sends one recipient with `sendEmail(...)`.
- Sends multiple recipients with `sendEmailsBulk(['emails' => ...])`.
- Uses HTML body when available, otherwise text body.
- Maps attachments to TheMarketer format (`name` + base64 `content`).

### Sender and reply-to fallback

If the outgoing message does not set `from` or `replyTo`, the transport uses values from the mailer config:

- `from` can be a string email or `['address' => '...']`
- `reply_to` can be a string email or `['address' => '...']`

## Constructor config shape

The Laravel config is passed directly to `new Client([...])` and supports the same keys as the core client:

- `customerId` (`string`, required)
- `restKey` (`string`, required)
- `trackingKey` (`string`, optional; default empty — set for events/tracking)
- `restUrl` (`string`, optional; default `https://t.themarketer.com`)
- `trackingUrl` (`string`, optional; default `https://t.themarketer.com`)
- `maxRetryAttempts` (`int`, optional; default `1`)
