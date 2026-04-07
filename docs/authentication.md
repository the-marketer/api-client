# Authentication

Autentificarea foloseste perechea `customerId` + `restKey`.

## How authentication works

La fiecare request, clientul adauga query params:

- `u` = `customerId`
- `k` = `restKey`

Acestea sunt injectate intern de `ApiGateway`.

## Initialize credentials

```php
use TheMarketer\ApiClient\Client;

$client = new Client(
    customerId: 'YOUR_CUSTOMER_ID',
    restKey: 'YOUR_REST_KEY',
);
```

## Validate credentials early

```php
$apiCheck = $client->checkApiCredentials();
$trackingCheck = $client->checkCredentials('YOUR_TRACKING_KEY');
```

## Security best practices

- Nu hardcoda credentialele in cod.
- Foloseste variabile de mediu (`.env`).
- Nu comita chei in repository.
- Roteaza cheile periodic.

## Env example

```php
$client = new Client(
    customerId: (string) getenv('THEMARKETER_CUSTOMER_ID'),
    restKey: (string) getenv('THEMARKETER_REST_KEY'),
);
```

## Next step

Mergi la [Orders](./orders.md) pentru exemple complete de comenzi.
