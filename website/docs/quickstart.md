---
sidebar_position: 3
title: Quickstart
---

## 1) Install

```bash
composer require themarketer/api-client
```

## 2) Initialize client

```php
use TheMarketer\ApiClient\Client;

$client = new Client(
    customerId: 'YOUR_CUSTOMER_ID',
    restKey: 'YOUR_REST_KEY',
    maxRetryAttempts: 1,
);
```

## 3) Check API credentials

```php
$result = $client->checkApiCredentials();
```

## 4) Save an order

```php
$response = $client->orders()->saveOrder([
    'order_no' => '1001',
    'email' => 'john@doe.com',
    'products' => [
        [
            'sku' => 'SKU-123',
            'quantity' => 1,
            'price' => 99.99,
        ],
    ],
]);
```
