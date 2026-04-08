---
sidebar_position: 13
title: Loyalty
---

Read loyalty info and add/remove points.

## Access module

```php
$loyaltyApi = $client->loyalty();
```

## `getInfo`

Returns loyalty info for a subscriber.

**Input**

- `email` (`string`, required, valid email)

**Response**

- `array`

```php
$result = $loyaltyApi->getInfo('john@doe.com');
```

## `managePoints`

Increases or decreases loyalty points.

**Input**

- `email` (`string`, required, valid email)
- `action` (`string`, required): `increase` or `decrease`
- `points` (`int`, required, positive)

**Response**

- `array`

```php
$result = $loyaltyApi->managePoints('john@doe.com', 'increase', 100);
```

