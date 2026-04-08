---
sidebar_position: 11
title: Events
---

Send custom events to enrich behavioral data.

## Access module

```php
$eventsApi = $client->events();
```

## `sendCustomEvent`

Sends a custom event.

**Input**

- `payload` (`array<string, mixed>`):
  - `email` (`string`, required, valid email)
  - `event` (`string`, required)

**Response**

- `array`

```php
$result = $eventsApi->sendCustomEvent([
    'email' => 'john@doe.com',
    'event' => 'wishlist_added',
]);
```

