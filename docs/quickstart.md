# Quickstart

Ghid rapid pentru prima integrare funcțională.

## 1) Install

```bash
composer require themarketer/api-client
```

## 2) Initialize client

`Client` primește un **array asociativ** (vezi `TheMarketer\ApiClient\Client::__construct`). În practică, pentru REST sunt necesare `customerId` și `restKey`; restul sunt opționale și se îmbină cu valorile implicite de mai jos.

| Cheie | Tip | Implicit | Rol |
| --- | --- | --- | --- |
| `customerId` | `string` | `''` | Identificator cont; trimis ca `u` în cererile REST. **Obligatoriu** în utilizare reală. |
| `restKey` | `string` | `''` | Secret REST; trimis ca `k`. **Obligatoriu** în utilizare reală. |
| `trackingKey` | `string` | `''` | Cheie tracking; necesară pentru apeluri bazate pe tracking (ex. unele metode `events()`). |
| `restUrl` | `string` | `https://t.themarketer.com` | URL de bază REST (`ApiGateway`). |
| `trackingUrl` | `string` | `https://t.themarketer.com` | URL de bază tracking (`TrackingGateway`). |
| `maxRetryAttempts` | `int` | `1` | Reîncercări la nivel HTTP în gateway-uri. |

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

Poți omite cheile opționale pentru a folosi implicitele. Detalii despre credențiale și tracking: [Authentication](./authentication.md).

## 3) Check API credentials

Pe `Client`, `checkApiCredentials()` returnează **`bool`**.

```php
$ok = $client->checkApiCredentials();
```

## 4) Save an order (minimal example)

Folosește câmpurile din DTO-ul `SaveOrder` (ex. `number`, `email_address`, linii produs cu `variation_sku`):

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

## 5) Handle exceptions

```php
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;

try {
    $client->checkApiCredentials();
} catch (UnauthorizedException $e) {
    // Invalid credentials
} catch (ApiException $e) {
    // Other API-level errors
}
```

## Next step

Mergi la [Authentication](./authentication.md) pentru detalii despre credențiale, tracking și configurare.
