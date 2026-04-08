---
sidebar_position: 15
title: Mobile push
---

Manage mobile push notification tokens through **`Client::mobilePush()`** (`MobilePushApi`).

## Access module

```php
$mobilePushApi = $client->mobilePush();
```

## `removeToken`

Removes a mobile push token.

**Input**

- `email` (`string`, required, valid email)
- `type` (`string`, required): `ios` or `android`

**Response**

- `array`

```php
$result = $mobilePushApi->removeToken(
    'john@doe.com',
    'android'
);
```

## `setToken`

Sets a mobile push token.

**Input**

- `email` (`string`, required, valid email)
- `token` (`string`, required)
- `type` (`string`, required): `ios` or `android`

**Response**

- `array`

```php
$result = $mobilePushApi->setToken(
    'john@doe.com',
    'DEVICE_TOKEN',
    'android'
);
```
