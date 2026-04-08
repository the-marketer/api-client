---
sidebar_position: 9
title: Transactionals
---

Send transactional email and SMS messages (and queued / bulk email).

## Access module

```php
$transactionalsApi = $client->transactionals();
```

## `sendEmail`

Sends a transactional email (immediate).

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

## `sendEmailAsync`

Same payload shape as `sendEmail`, but uses the **queue** endpoint (`/transactional/queue-send-email`).

```php
$result = $transactionalsApi->sendEmailAsync([
    'to' => 'john@doe.com',
    'subject' => 'Queued',
    'body' => '<p>Later</p>',
]);
```

## `sendEmailsBulk`

Sends multiple emails in one request. Payload:

- `emails`: non-empty list of objects, each valid as `sendEmail` payload.

```php
$result = $transactionalsApi->sendEmailsBulk([
    'emails' => [
        ['to' => 'a@example.com', 'subject' => 'S1', 'body' => 'B1'],
        ['to' => 'b@example.com', 'subject' => 'S2', 'body' => 'B2'],
    ],
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
