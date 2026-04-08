# Authentication

Autentificarea folosește perechea `customerId` + `restKey` pentru API-ul **REST**.

## Cum funcționează REST

La fiecare request REST, clientul adaugă în query:

- `u` = `customerId`
- `k` = `restKey`

Acestea sunt injectate de `ApiGateway`.

## Tracking (evenimente comportamentale)

Pentru host-ul de tracking, este necesară și o cheie **`trackingKey`** în configurația `Client`. Gateway-ul de tracking folosește alt set de parametri de autentificare (`k` + `api_key`).

## Inițializare `Client`

Constructorul acceptă **un singur argument**: array asociativ.

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

## Verificare credențiale

Pe `Client`, `checkApiCredentials()` și `checkCredentials()` returnează **`bool`** (`true` când răspunsul API se decodează la array gol `[]`).

```php
$apiOk = $client->checkApiCredentials();
$trackingOk = $client->checkCredentials('YOUR_TRACKING_KEY');
```

## Security best practices

- Nu hardcoda credențialele în cod.
- Folosește variabile de mediu (`.env`).
- Nu comita chei în repository.
- Rotează cheile periodic.

## Env example

```php
$client = new Client([
    'customerId' => (string) getenv('THEMARKETER_CUSTOMER_ID'),
    'restKey' => (string) getenv('THEMARKETER_REST_KEY'),
    'trackingKey' => (string) getenv('THEMARKETER_TRACKING_KEY'),
]);
```

## Next step

Mergi la [Orders](./orders.md) pentru exemple complete de comenzi.
