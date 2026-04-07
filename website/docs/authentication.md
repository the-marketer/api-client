---
sidebar_position: 4
title: Authentication
---

Authentication uses the `customerId` + `restKey` pair.

## How it works

Every request includes:

- `u` = `customerId`
- `k` = `restKey`

These query params are injected by `ApiGateway`.

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

- Do not hardcode credentials in source code.
- Use environment variables.
- Never commit keys to Git.
