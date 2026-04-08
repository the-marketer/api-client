---
sidebar_position: 7
title: Campaigns
---

Work with campaign listing, creation, and reporting.

## Access module

```php
$campaigns = $client->campaigns();
```

## Methods

- `list(array $payload = [])`
- `create(array $payload)`
- `getEmailReport(string $id)`
- `getLatestCampaign(?int $limit = null)`

## Example

```php
$latest = $campaigns->getLatestCampaign(5);
```
