---
sidebar_position: 16
title: Credentials and Utilities
---

These methods are exposed directly on `Client` and delegate internally to `CredentialsClient` (`src/Api/CredentialsClient.php`).

## Return types on `Client` vs raw API

- On **`Client`**, `checkCredentials()` and `checkApiCredentials()` return **`bool`**: `true` when the underlying client receives a JSON body that decodes to an **empty array** `[]` (treated as success in this facade), `false` otherwise.
- Other helpers return **`array`** or **`string`** as documented below.
- If you need the **decoded JSON `array`** from check endpoints (not a boolean), call the same methods on `CredentialsClient` with a configured `ApiContext` (see package tests for patterns).

## `checkApiCredentials`

Verifies REST API credentials.

**Input**

- none

**Response**

- `bool` (on `Client`)

```php
$ok = $client->checkApiCredentials();
```

## `checkCredentials`

Verifies credentials using a **tracking** key (sent in the JSON body as required by the API).

**Input**

- `trackingKey` (`string`, required)

**Response**

- `bool` (on `Client`)

```php
$ok = $client->checkCredentials('YOUR_TRACKING_KEY');
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

Returns referral link content (raw response body, not JSON-decoded to `array`).

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

## `config()`

Returns the immutable `Config` (customer id, rest key, URLs, `baseRestUrl()`, tracking key, etc.).

```php
$customerId = $client->config()->customerId();
$restBase = $client->config()->baseRestUrl();
```
