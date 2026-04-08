# Quickstart

Ghid rapid pentru prima integrare functionala.

## 1) Install

```bash
composer require themarketer/api-client
```

## 2) Initialize client

```php
use TheMarketer\ApiClient\Client;

$client = new Client(
    customerId: 'YOUR_CUSTOMER_ID',
    restKey: 'YOUR_REST_KEY'
);
```

## 3) Check API credentials

```php
$result = $client->checkApiCredentials();
```

## 4) Save an order (minimal example)

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

## 5) Handle exceptions

```php
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;

try {
    $response = $client->checkApiCredentials();
} catch (UnauthorizedException $e) {
    // Invalid credentials
} catch (ApiException $e) {
    // Other API-level errors
}
```

## Next step

Mergi la [Authentication](./authentication.md) pentru detalii despre credentiale si configurare.
