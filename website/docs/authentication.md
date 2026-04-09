---
sidebar_position: 4
title: Authentication
---

Authentication uses the `customerId` + `restKey` pair for **REST** API calls.

## How REST requests are signed

Every REST request includes query parameters:

- `u` = `customerId`
- `k` = `restKey`

These are injected by `ApiGateway` (see `src/Gateways/ApiGateway.php`).

## Tracking (behavioral events)

Endpoints that use the **tracking** base URL also need a **tracking key** in configuration (`trackingKey`). The tracking gateway adds its own auth query (`k` = tracking key, `api_key` = rest key). Configure it when using `events()` methods that hit the tracking host.

## Initialize the client

`Client` accepts a **single associative array** (not separate constructor parameters):

```php
use TheMarketer\ApiClient\Client;

$client = new Client([
    'customerId' => 'YOUR_CUSTOMER_ID',
    'restKey' => 'YOUR_REST_KEY',
    'trackingKey' => 'YOUR_TRACKING_KEY',
    // Optional:
    'restUrl' => 'https://t.themarketer.com',
    'trackingUrl' => 'https://t.themarketer.com',
    'maxRetryAttempts' => 1,
]);
```

## Validate credentials early

`checkApiCredentials()` and `checkCredentials()` on `Client` return **`bool`**: `true` when the API response body decodes to an **empty array** (success), `false` otherwise.

```php
$apiOk = $client->checkApiCredentials();
$trackingOk = $client->checkCredentials('YOUR_TRACKING_KEY');
```

For the raw decoded JSON (`array`), use `CredentialsClient` via the same HTTP stack (see [Credentials and Utilities](./credentials-utilities.md)).

## Security best practices

- Do not hardcode credentials in source code.
- Use environment variables.
- Never commit keys to Git.
