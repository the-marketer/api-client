---
sidebar_position: 14
title: Reviews
---

Manage product and merchant reviews.

## Access module

```php
$reviewsApi = $client->reviews();
```

## `create`

Creates a review payload.

**Input**

- `payload` (`array<string, mixed>`):
  - `order_id` (`string`, required)
  - `review_date` (`string`, required)
  - `order_rating` (`?string`)
  - `order_review` (`?string`)
  - `product_rating` (`?array`)
  - `product_review` (`?array`)
  - `media_files` (`?array`)

**Response**

- `array`

```php
$result = $reviewsApi->create([
    'order_id' => '1001',
    'review_date' => '2026-01-01',
    'order_rating' => '5',
    'order_review' => 'Fast delivery',
]);
```

## `get`

Returns product reviews feed content.

**Input**

- `query` (`array<string, mixed>`, all optional):
  - `t` (`?int`, positive)
  - `page` (`?int`, positive)
  - `perPage` (`?int`, positive)

**Response**

- `string`

```php
$result = $reviewsApi->get([
    'page' => 1,
    'perPage' => 20,
]);
```

## `merchantAddReview`

Adds a merchant review.

**Input**

- `payload` (`array<string, mixed>`):
  - `email` (`string`, required, valid email)
  - `product_id` (`string|int`, required)
  - `name` (`?string`)
  - `date_created` (`?string`)
  - `rating` (`?int`, positive or zero)
  - `content` (`?string`)

**Response**

- `array`

```php
$result = $reviewsApi->merchantAddReview([
    'email' => 'john@doe.com',
    'product_id' => 123,
    'rating' => 5,
    'content' => 'Great product',
]);
```

## `merchantProSetting`

Updates Merchant Pro settings.

**Input**

- `payload` (`array<string, mixed>`, all optional):
  - `product_feed_url` (`?string`)
  - `inventory_feed_url` (`?string`)
  - `order_feed_url` (`?string`)
  - `api_key` (`?string`)
  - `api_password` (`?string`)

**Response**

- `array`

```php
$result = $reviewsApi->merchantProSetting([
    'product_feed_url' => 'https://shop.example.com/products.xml',
    'inventory_feed_url' => 'https://shop.example.com/inventory.xml',
    'order_feed_url' => 'https://shop.example.com/orders.xml',
]);
```

