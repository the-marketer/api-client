---
sidebar_position: 12
title: Coupons
---

Fetch available coupons and save coupon-related payloads.

## Access module

```php
$couponsApi = $client->coupons();
```

## `getAvailableCoupons`

Returns available coupons for an email.

**Input**

- `email` (`string`, required, valid email)

**Response**

- `array`

```php
$result = $couponsApi->getAvailableCoupons('john@doe.com');
```

## `save`

Saves a coupon payload.

**Input**

- `payload` (`array<string, mixed>`):
  - `code` (`string`, required)
  - `type` (`string`, required)
  - `value` (`string`, required)
  - `expiration_date` (`string`, required, date)
  - `email` (`?string`, optional)

**Response**

- `array`

```php
$result = $couponsApi->save([
    'code' => 'WELCOME10',
    'type' => 'fixed',
    'value' => '10',
    'expiration_date' => '2026-12-31',
    'email' => 'john@doe.com',
]);
```

