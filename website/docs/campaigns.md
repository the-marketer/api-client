---
sidebar_position: 7
title: Campaigns
---

List campaigns, create new campaigns, fetch email reports, and load the latest campaign snapshot. All calls use the **REST** gateway (`k` / `u` query auth). Initialize `Client` with an array config (see [Authentication](./authentication.md)).

## Access module

```php
$campaignsApi = $client->campaigns();
```

---

## `list`

Lists campaigns with optional filters. Uses **POST** to `/campaigns/list` with a JSON body built from the `ListCampaign` DTO.

**Input**

- `payload` (`array<string, mixed>`, optional; default `[]`). All keys are optional strings; empty values are omitted from the body:
  - `filters` (`?string`)
  - `search` (`?string`)
  - `type` (`?string`)
  - `start_date` (`?string`)
  - `page` (`?string`)
  - `limit` (`?string`)

**Response**

- `array`

```php
$result = $campaignsApi->list();

$result = $campaignsApi->list([
    'search' => 'spring',
    'type' => 'email',
    'page' => '1',
    'limit' => '20',
]);
```

---

## `create`

Creates a campaign. Payload is validated by `CreateCampaign` and nested DTOs, then sent as JSON to `/campaigns/create`.

**Input**

- `payload` (`array<string, mixed>`), top-level:
  - `type` (`string`, required): one of `sms`, `email`, `push`
  - `mode` (`string`, required): one of `ecommerce`, `regular`, `plaintext`
  - `sender` (`array`, required):
    - `name` (`string`, required)
    - `sender` (`string`, required, email)
    - `reply_to` (`string`, required, email)
  - `audience` (`array`, required):
    - `audience_type` (`string`, required): currently `all`
    - `smart_sending` (`bool`, required)
  - `subject` (`array`, required):
    - `name` (`string`, required)
    - `subject_line` (`string`, required)
    - `preview_text` (`string`, required)
  - `content` (`array`, required):
    - `html` (`string`, required): HTML body (max length enforced in DTO)
  - `scheduling` (`array`, required):
    - `send_at` (`string`, required): datetime `Y-m-d H:i`
    - `use_optimal_time` (`int`, required): `0` or `1`
    - `optimize_for` (`string`, required): `opening` or `buying`
  - `tracking` (`array`, required):
    - `utm_campaign` (`string`, required)
    - `utm_medium` (`string`, required)
    - `utm_source` (`string`, required)

**Response**

- `array`

```php
$result = $campaignsApi->create([
    'type' => 'email',
    'mode' => 'regular',
    'sender' => [
        'name' => 'Shop',
        'sender' => 'shop@example.com',
        'reply_to' => 'support@example.com',
    ],
    'audience' => [
        'audience_type' => 'all',
        'smart_sending' => false,
    ],
    'subject' => [
        'name' => 'Spring',
        'subject_line' => 'Hello',
        'preview_text' => 'Preview',
    ],
    'content' => [
        'html' => '<p>Hi</p>',
    ],
    'scheduling' => [
        'send_at' => '2026-06-01 12:00',
        'use_optimal_time' => 0,
        'optimize_for' => 'opening',
    ],
    'tracking' => [
        'utm_campaign' => 'spring-2026',
        'utm_medium' => 'email',
        'utm_source' => 'newsletter',
    ],
]);
```

---

## `getEmailReport`

Returns the email report for a campaign.

**Input**

- `id` (`string`, required): campaign identifier (embedded in the URL path; special characters should be safe for path segments—see API behavior).

**Response**

- `array`

```php
$result = $campaignsApi->getEmailReport('99');
```

Internally this calls **GET** `/campaigns/{id}/email/get-report` with standard REST auth query params.

---

## `getLatestCampaign`

Returns the latest campaign data. Uses **GET** `/get-latest-campaign`.

**Input**

- `limit` (`?int`, optional): when set, must be **positive**; sent as query param `limit`.

**Response**

- `array` (decoded JSON; shape depends on API)

```php
$result = $campaignsApi->getLatestCampaign();

$result = $campaignsApi->getLatestCampaign(5);
```

---

## Validation and errors

- Invalid or incomplete payloads raise `TheMarketer\ApiClient\Exception\ValidationException` before the HTTP request.
- Failed API responses are mapped to `UnauthorizedException`, `CustomerNotFoundException`, `MethodNotAllowedException`, or `ApiException` as documented in [Errors and Troubleshooting](./errors.md).

## Source references

- API methods: `src/Api/CampaignsApi.php`
- DTOs: `src/DTO/Campaigns/*.php`
- Examples in tests: `tests/CampaignsApiTest.php`
