---
sidebar_position: 3
title: Quickstart
---

## 1) Install

```bash
composer require themarketer/api-client
```

## 2) Initialize client

`Client` accepts a **single associative array** (see `TheMarketer\ApiClient\Client::__construct`). Only `customerId` and `restKey` are required for REST calls; the rest are optional and merge with the defaults below.

| Key | Type | Default | Purpose |
| --- | --- | --- | --- |
| `customerId` | `string` | `''` | Account identifier; sent as `u` on REST requests. **Required** for real usage. |
| `restKey` | `string` | `''` | REST API secret; sent as `k` on REST requests. **Required** for real usage. |
| `trackingKey` | `string` | `''` | Tracking / behavioral API key; needed for tracking-based calls (e.g. some `events()` flows). |
| `restUrl` | `string` | `https://t.themarketer.com` | Base URL for REST (`ApiGateway`). |
| `trackingUrl` | `string` | `https://t.themarketer.com` | Base URL for tracking (`TrackingGateway`). |
| `maxRetryAttempts` | `int` | `1` | Retries per gateway HTTP layer when requests fail transiently. |

```php
use TheMarketer\ApiClient\Client;

$client = new Client([
    'customerId' => 'YOUR_CUSTOMER_ID',
    'restKey' => 'YOUR_REST_KEY',
    'trackingKey' => 'YOUR_TRACKING_KEY',
    'restUrl' => 'https://t.themarketer.com',
    'trackingUrl' => 'https://t.themarketer.com',
    'maxRetryAttempts' => 1,
]);
```

Omit optional keys to use the defaults above. For credential signing details, see [Authentication](./authentication.md).

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
