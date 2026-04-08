---
sidebar_position: 15
title: App Push
---

Manage push notification tokens.

## Access module

```php
$mobilePushApi = $client->mobilePush();
```

## `removeToken`

Removes an app push token.

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

Sets an app push token.

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

