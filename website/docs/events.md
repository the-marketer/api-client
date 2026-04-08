---
sidebar_position: 11
title: Events
---

Send standard and custom behavioral events. There are two integration styles:

1. **REST — `sendCustomApi`**  
   POST către **`/custom_events`** pe host-ul REST. Autentificare query: **`k`** = rest key, **`u`** = customer id (`ApiGateway`). Payload minim: `email`, `event`.

2. **Tracking — toate celelalte metode**  
   Folosesc **`TrackingGateway`**: POST către **`/t/r`** (sau **GET** pentru `serveJavascript`). Necesită **`trackingKey`** setat în configurația `Client` (vezi [Authentication](./authentication.md)). Query: **`k`** = tracking key, **`api_key`** = rest key.

```php
$eventsApi = $client->events();
```

---

## `sendCustomApi`

Eveniment personalizat prin API-ul REST (fără câmpurile complete de tracking din `CustomEvent`).

**Transport**

- **POST** `{baseRestUrl}custom_events` (cu query `k`, `u`).

**Input — `payload`**

- `email` (`string`, required, email valid)
- `event` (`string`, required)

**Response**

- `array`

```php
$result = $eventsApi->sendCustomApi([
    'email' => 'user@example.com',
    'event' => 'newsletter_click',
]);
```

---

## `sendCustom`

Eveniment personalizat pe **tracking** (`CustomEvent`).

**Transport**

- **POST** tracking **`/t/r`**

**Input — `payload`**

- `did` (`string`, required) — device / session id
- `email` (`string`, required, email valid)
- `event` (`string`, required)
- `url` (`string`, required, URL valid)
- `http_user_agent` (`string`, required)
- `remote_addr` (`string`, required)
- `source` (`?string`, optional)

**Response**

- `array`

```php
$result = $eventsApi->sendCustom([
    'did' => 'device-abc',
    'email' => 'user@example.com',
    'event' => 'product_viewed',
    'url' => 'https://shop.example.com/p/1',
    'http_user_agent' => 'Mozilla/5.0',
    'remote_addr' => '203.0.113.10',
]);
```

---

## `viewHomepage`

Vizualizare homepage / pagină generică (`ViewHomepageEvent`).

**Transport**

- **POST** tracking **`/t/r`**

**Input — `payload`**

- `did` (`string`, required)
- `event` (`string`, required) — ex. nume eveniment agreat cu backend
- `url` (`string`, required, URL valid)
- `http_user_agent` (`string`, required)
- `remote_addr` (`string`, required)
- `source` (`?string`, optional)

**Response**

- `array`

```php
$result = $eventsApi->viewHomepage([
    'did' => 'device-abc',
    'event' => 'view_homepage',
    'url' => 'https://shop.example.com/',
    'http_user_agent' => 'Mozilla/5.0',
    'remote_addr' => '203.0.113.10',
]);
```

---

## `setEmail`

Asociere / setare email în fluxul de tracking (`SetEmailEvent`).

**Transport**

- **POST** tracking **`/t/r`**

**Input — `payload`**

- `did` (`string`, required)
- `event` (`string`, required)
- `email_address` (`string`, required, email valid)
- `firstname` (`string`, required)
- `lastname` (`string`, required)
- `phone` (`string`, required)
- `url` (`string`, required, URL valid)
- `http_user_agent` (`string`, required)
- `remote_addr` (`string`, required)
- `source` (`?string`, optional)

**Response**

- `array`

---

## `viewProduct`

Vizualizare produs (`ViewProductEvent`).

**Transport**

- **POST** tracking **`/t/r`**

**Input — `payload`**

- `did` (`string`, required)
- `event` (`string`, required)
- `product_id` (`string`, required)
- `url` (`string`, required, URL valid)
- `http_user_agent` (`string`, required)
- `remote_addr` (`string`, required)
- `source` (`?string`, optional)

**Response**

- `array`

```php
$result = $eventsApi->viewProduct([
    'did' => 'device-abc',
    'event' => 'view_product',
    'product_id' => '42',
    'url' => 'https://shop.example.com/p/42',
    'http_user_agent' => 'Mozilla/5.0',
    'remote_addr' => '203.0.113.10',
]);
```

---

## `addToCart`, `removeFromCart`, `addToWishlist`, `removeFromWishlist`

Operații coș / wishlist; toate folosesc același DTO **`ProductLineEvent`**. Diferența este valoarea câmpului **`event`** (și semantica în backend).

**Transport**

- **POST** tracking **`/t/r`**

**Input — `payload`**

- `did` (`string`, required)
- `event` (`string`, required) — ex. `add_to_cart`, `remove_from_cart`, …
- `product_id` (`int`, required, pozitiv) — poate fi trimis ca string numeric; este normalizat
- `quantity` (`int`, required, pozitiv)
- `variation` (`array`, required):
  - `id` (`string`, required)
  - `sku` (`string`, required)
- `http_user_agent` (`string`, required)
- `url` (`string`, required, URL valid)
- `remote_addr` (`string`, required)
- `source` (`?string`, optional)

**Response**

- `array`

```php
$line = [
    'did' => 'device-abc',
    'event' => 'add_to_cart',
    'product_id' => 100,
    'quantity' => 1,
    'variation' => ['id' => 'var-1', 'sku' => 'SKU-1'],
    'http_user_agent' => 'Mozilla/5.0',
    'url' => 'https://shop.example.com/p/100',
    'remote_addr' => '203.0.113.10',
];

$eventsApi->addToCart($line);
$eventsApi->removeFromCart([...]); // același shape, alt `event`
```

---

## `initiateCheckout`

Start checkout; folosește **`InitiateCheckoutEvent`**, același set de câmpuri ca **`ViewHomepageEvent`**.

**Transport**

- **POST** tracking **`/t/r`**

**Input — `payload`**

- `did`, `event`, `url`, `http_user_agent`, `remote_addr` (required, ca la `viewHomepage`)
- `source` (`?string`, optional)

**Response**

- `array`

```php
$result = $eventsApi->initiateCheckout([
    'did' => 'device-abc',
    'event' => 'initiate_checkout',
    'url' => 'https://shop.example.com/checkout',
    'http_user_agent' => 'Mozilla/5.0',
    'remote_addr' => '203.0.113.10',
]);
```

---

## `search`

Căutare site (`SearchEvent`).

**Transport**

- **POST** tracking **`/t/r`**

**Input — `payload`**

- `did` (`string`, required)
- `event` (`string`, required)
- `search_term` (`string`, required)
- `url` (`string`, required, URL valid)
- `http_user_agent` (`string`, required)
- `remote_addr` (`string`, required)
- `source` (`?string`, optional)

**Response**

- `array`

```php
$result = $eventsApi->search([
    'did' => 'device-abc',
    'event' => 'search',
    'search_term' => 'running shoes',
    'url' => 'https://shop.example.com/search?q=running',
    'http_user_agent' => 'Mozilla/5.0',
    'remote_addr' => '203.0.113.10',
]);
```

---

## `serveJavascript`

Încarcă resursa JavaScript de tracking pentru o cheie dată.

**Transport**

- **GET** tracking **`/t/j/{trackingKey}`**  
  Parametrul metodei este același șir folosit în path; în plus, DTO-ul validează lungimea cheii (**6–20** caractere) ca `k`.

**Input**

- `trackingKey` (`string`, required) — 6–20 caractere

**Response**

- `array` (JSON decodat de gateway)

```php
$result = $eventsApi->serveJavascript('abcdef123456');
```

---

## Validare și erori

- Payload-urile incomplete sau invalide → `ValidationException` înainte de rețea.
- Lipsă **`trackingKey`** în config la apeluri tracking → `ValidationException` (vezi [Errors](./errors.md)).
- Surse: `src/Api/EventsApi.php`, `src/DTO/Events/`, `tests/EventsApiTest.php`.
