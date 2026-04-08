---
sidebar_position: 9
title: Transactionals
---

Send transactional email and SMS messages.

## Access module

```php
$transactionalsApi = $client->transactionals();
```

## `sendEmail`

Sends a transactional email.

**Input**

- `payload` (`array<string, mixed>`):
  - `to` (`string`, required, valid email)
  - `subject` (`string`, required)
  - `body` (`string`, required)
  - `from` (`?string`)
  - `reply_to` (`?string`, valid email when present)
  - `attachments` (`?array`)

**Response**

- `array`

```php
$result = $transactionalsApi->sendEmail([
    'to' => 'john@doe.com',
    'subject' => 'Order confirmation',
    'body' => '<p>Your order was received.</p>',
    'from' => 'no-reply@shop.example',
    'reply_to' => 'support@shop.example',
]);
```

## `sendSms`

Sends a transactional SMS.

**Input**

- `to` (`string`, required)
- `content` (`string`, required)

**Response**

- `array`

```php
$result = $transactionalsApi->sendSms(
    '+40123456789',
    'Your order has been shipped.',
);
```

