---
sidebar_position: 3
title: Quickstart
---

## 1) Install

```bash
composer require themarketer/api-client
```

## 2) Initialize client

Pass an **array** of options. Required keys: `customerId`, `restKey`.

```php
use TheMarketer\ApiClient\Client;

$client = new Client([
    'customerId' => 'YOUR_CUSTOMER_ID',
    'restKey' => 'YOUR_REST_KEY',
    'maxRetryAttempts' => 1, // optional; default 1
]);
```

## 3) Check API credentials

On `Client`, this returns **`bool`** (`true` if the API indicates success with an empty JSON array body).

```php
$ok = $client->checkApiCredentials();
```

## 4) Save an order

Use the field names expected by `SaveOrder` / `OrdersApi::saveOrder()` (for example `number`, `email_address`, and line items with `variation_sku`):

```php
$response = $client->orders()->saveOrder([
    'number' => 1001,
    'email_address' => 'john@doe.com',
    'phone' => '+40123456789',
    'firstname' => 'John',
    'lastname' => 'Doe',
    'city' => 'Bucharest',
    'county' => 'RO',
    'address' => 'Street 1',
    'discount_value' => 0.0,
    'discount_code' => '-',
    'shipping' => 0.0,
    'tax' => 0.0,
    'total_value' => 99.99,
    'products' => [
        [
            'product_id' => 123,
            'price' => 99.99,
            'quantity' => 1,
            'variation_sku' => 'SKU-123',
        ],
    ],
]);
```

See [Orders](./orders.md) for retail (`saveOrderRetail`) and other methods.
