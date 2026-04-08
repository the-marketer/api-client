---
sidebar_position: 16
title: Credentials and Utilities
---

These methods are exposed directly on `Client` and delegated internally to `CredentialsClient`.

## `checkApiCredentials`

Checks API credentials.

**Input**

- none

**Response**

- `array`

```php
$result = $client->checkApiCredentials();
```

## `checkCredentials`

Checks credentials using a tracking key.

**Input**

- `trackingKey` (`string`, required)

**Response**

- `array`

```php
$result = $client->checkCredentials('TRACKING_KEY');
```

## `getCosts`

Returns cost information.

**Input**

- none

**Response**

- `array`

```php
$result = $client->getCosts();
```

## `getDeliveryLogs`

Returns delivery logs.

**Input**

- `payload` (`array<string, mixed>`):
  - `email` (`string`, required, valid email)
  - `per_page` (`?int`, optional, between 1 and 100)
  - `page` (`?int`, optional, positive)
  - `start` (`?string`, optional, date)
  - `end` (`?string`, optional, date)

**Response**

- `array`

```php
$result = $client->getDeliveryLogs([
    'email' => 'john@doe.com',
    'per_page' => 20,
    'page' => 1,
]);
```

## `getEnteredAutomation`

Returns entered automation data.

**Input**

- `payload` (`array<string, mixed>`):
  - `date` (`string`, required, format `Y-m-d`)
  - `page` (`?int`, optional, positive)
  - `perPage` (`?int`, optional, between 1 and 100)

**Response**

- `array`

```php
$result = $client->getEnteredAutomation([
    'date' => '2026-01-31',
    'page' => 1,
    'perPage' => 20,
]);
```

## `getRealtimeVisitors`

Returns realtime visitors data.

**Input**

- none

**Response**

- `array`

```php
$result = $client->getRealtimeVisitors();
```

## `getReferralLink`

Returns referral link content.

**Input**

- `email` (`?string`, optional, valid email)

**Response**

- `string`

```php
$result = $client->getReferralLink('john@doe.com');
```

## `getSmsCredit`

Returns SMS credit information.

**Input**

- none

**Response**

- `array`

```php
$result = $client->getSmsCredit();
```

