---
sidebar_position: 5
title: Orders
---

The Orders module handles order-related operations.

## Access module

```php
$ordersApi = $client->orders();
```

## Save order

```php
$payload = [
    'order_no' => '1001',
    'email' => 'john@doe.com',
    'products' => [
        [
            'sku' => 'SKU-123',
            'quantity' => 1,
            'price' => 99.99,
        ],
    ],
];

$result = $ordersApi->saveOrder($payload);
```

## Alias

```php
$result = $ordersApi->save($payload);
```

## Recommended payload quality

- Keep `order_no` unique.
- Use a valid customer email.
- Send at least one product entry.
