---
sidebar_position: 8
title: Products
---

Synchronize products, categories, and brands.

## Access module

```php
$productsApi = $client->products();
```

## `createProduct`

Creates a product.

**Input**

- `payload` (`array<string, mixed>`):
  - `id` (`string`, required)
  - `sku` (`string`, required)
  - `name` (`string`, required)
  - `description` (`string`, required)
  - `url` (`string`, required)
  - `main_image` (`string`, required)
  - `category` (`string`, required)
  - `brand` (`string`, required)
  - `acquisition_price` (`float`, required)
  - `price` (`float`, required)
  - `sale_price` (`string`, required)
  - `availability` (`int`, required)
  - `stock` (`int`, required)
  - `media_gallery` (`array`, required, exactly 2 string items)
  - `created_at` (`string`, required)
  - `extra_attributes` (`?array<string, string>`)
  - `sale_price_start_date` (`?string`)
  - `sale_price_end_date` (`?string`)

**Response**

- `array`

```php
$result = $productsApi->createProduct([
    'id' => 'SKU-123',
    'sku' => 'SKU-123',
    'name' => 'Product name',
    'description' => 'Product description',
    'url' => 'https://shop.example.com/products/sku-123',
    'main_image' => 'https://shop.example.com/images/sku-123.jpg',
    'category' => 'Category',
    'brand' => 'Brand',
    'acquisition_price' => 50.0,
    'price' => 99.99,
    'sale_price' => '89.99',
    'availability' => 1,
    'stock' => 10,
    'media_gallery' => [
        'https://shop.example.com/images/sku-123.jpg',
        'https://shop.example.com/images/sku-123-2.jpg',
    ],
    'created_at' => '2026-01-01T10:00:00Z',
]);
```

## `syncBrands`

Creates or updates a brand.

**Input**

- `payload` (`array<string, mixed>`):
  - `id` (`string`, required)
  - `name` (`string`, required)
  - `url` (`string`, required)
  - `image_url` (`string`, required)

**Response**

- `array`

```php
$result = $productsApi->syncBrands([
    'id' => 'brand-10',
    'name' => 'Brand',
    'url' => 'https://shop.example.com/brands/brand-10',
    'image_url' => 'https://shop.example.com/images/brands/brand-10.jpg',
]);
```

## `syncCategories`

Creates or updates a category.

**Input**

- `payload` (`array<string, mixed>`):
  - `id` (`string`, required)
  - `name` (`string`, required)
  - `hierarchy` (`string`, required)
  - `url` (`string`, required)
  - `image_url` (`string`, required)

**Response**

- `array`

```php
$result = $productsApi->syncCategories([
    'id' => 'cat-10',
    'name' => 'Category',
    'hierarchy' => 'Root > Category',
    'url' => 'https://shop.example.com/category/cat-10',
    'image_url' => 'https://shop.example.com/images/categories/cat-10.jpg',
]);
```

## `updateProduct`

Updates an existing product.

**Input**

- `payload` (`array<string, mixed>`):
  - `id` (`string`, required)
  - `sku` (`string`, required)
  - `name` (`?string`)
  - `description` (`?string`)
  - `url` (`?string`)
  - `main_image` (`?string`)
  - `category` (`?string`)
  - `brand` (`?string`)
  - `acquisition_price` (`?float`)
  - `price` (`?float`)
  - `sale_price` (`?string`)
  - `availability` (`?int`)
  - `stock` (`?int`)
  - `media_gallery` (`?array`, max 2 string items)
  - `created_at` (`?string`)
  - `extra_attributes` (`?array<string, string>`)
  - `sale_price_start_date` (`?string`)
  - `sale_price_end_date` (`?string`)

**Response**

- `array`

```php
$result = $productsApi->updateProduct([
    'id' => 'SKU-123',
    'sku' => 'SKU-123',
    'price' => 89.99,
    'stock' => 7,
]);
```
