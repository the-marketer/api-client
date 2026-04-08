---
sidebar_position: 8
title: Products
---

Synchronize products, categories, and brands.

## Access module

```php
$products = $client->products();
```

## Methods

- `createProduct(array $payload)`
- `updateProduct(array $payload)`
- `syncCategories(array $payload)`
- `syncBrands(array $payload)`

## Example

```php
$products->createProduct([
    'id' => 'SKU-123',
    'name' => 'Product name',
    'price' => 99.99,
]);
```
