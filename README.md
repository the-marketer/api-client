# The Marketer API Client (PHP)

Client PHP pentru API-ul The Marketer. Folosește [Guzzle](https://docs.guzzlephp.org/) pentru HTTP și [Spatie Laravel Data](https://spatie.be/docs/laravel-data) pentru validarea payload-urilor.

## Instalare

```bash
composer require themarketer/api-client
```

În proiectul curent:

```bash
composer install
```

## Utilizare

Nu este nevoie să instanțiezi Guzzle sau să setezi manual URL-ul API: clientul folosește implicit `https://t.themarketer.com/api/v1` și un `GuzzleHttp\Client` intern.

```php
use TheMarketer\ApiClient\Client;

$client = new Client(
    customerId: 'abc123',
    restKey: 'xyz789',
    apiKey: 'your-tracking-or-api-key',
);

$client->subscribers()->add([...]);
$client->orders()->save([...]);
$client->transactionals()->sendEmail([...]);
```

Parametri obligatorii:

| Parametru | Rol |
|-----------|-----|
| `customerId` | ID-ul contului / customer (`u` în query la autentificare) |
| `restKey` | Cheia REST (`k` în query) |
| `apiKey` | Cheia de tracking / API (ex. pentru `checkCredentials` și alte rute care o cer) |

Metodele vechi (`addSubscriber`, `saveOrder`) rămân disponibile; `add` și `save` sunt aliasuri scurte.

## Erori și excepții

Mesajul afișat vine de obicei din câmpul JSON `message` al răspunsului (vezi `HttpClient::decodeApiErrorMessage()`).

**Înainte de request (validare locală)**

- **Validare DTO** (payload invalid): `Illuminate\Validation\ValidationException`.
- **Credențiale lipsă în client** (ex. lipsesc `customerId` / `restKey` pentru rute care le cer): `TheMarketer\ApiClient\Exception\ValidationException` (cod implicit 400).

**După request — mapare după status HTTP** (`HttpClient::throwForErrorResponse()`)

| Status | Excepție |
|--------|----------|
| **401** | `UnauthorizedException` |
| **404** | `CustomerNotFoundException` |
| **405** | `MethodNotAllowedException` |
| **Orice alt 4xx/5xx** (inclusiv **400**, **422**, **403**, **5xx**) | `ApiException` — folosește `$e->getHttpStatusCode()` și `$e->getMessage()` |

Nu există în pachet clase separate precum `AuthException` sau `AccountInactiveException`: două situații diferite cu același status (ex. două cazuri de **401**) se deosebesc prin **mesajul** returnat de API, tot într-o `UnauthorizedException` sau prin analiza mesajului în `catch`.

**Decodare JSON la succes**

- Dacă răspunsul nu e JSON valid când metoda așteaptă JSON: `\JsonException`.

Detalii despre câmpurile obligatorii pentru fiecare acțiune: clasele din `src/DTO/`.

---

## Credențiale și utilitare (pe `Client`)

Aceste metode nu folosesc `subscribers()`, `orders()` etc., ci sunt apelate direct pe `Client`.

### `checkCredentials()`

Verifică credențialele (necesită `u`, `k` și `r` — tracking key). Returnează `array` (JSON decodat de la API, ex. `['success' => true]`).

```php
$result = $client->checkCredentials();
```

### `checkApiCredentials()`

Returnează `array` (JSON decodat). Erorile HTTP folosesc aceleași excepții ca în tabelul de mai sus.

```php
$result = $client->checkApiCredentials();
```

### `getCosts()`, `getRealtimeVisitors()`, `getSmsCredit()`

Returnează `array` (JSON decodat de la API).

```php
$costs = $client->getCosts();
$visitors = $client->getRealtimeVisitors();
$credit = $client->getSmsCredit();
```

### `getReferralLink(?string $email = null)`

Returnează `string` (conținutul răspunsului).

```php
$link = $client->getReferralLink();
$link = $client->getReferralLink('user@example.com');
```

### `getDeliveryLogs(array $payload)`

`email` este obligatoriu; opțional: `per_page`, `page`, `start`, `end`. Validarea email poate folosi și verificare DNS — folosește adrese reale dacă primești erori de validare.

```php
$logs = $client->getDeliveryLogs([
    'email' => 'user@gmail.com',
    'per_page' => 25,
    'page' => 1,
]);
```

### `getEnteredAutomation(array $payload)`

`date` obligatoriu (`Y-m-d`); opțional `page`, `perPage`.

```php
$data = $client->getEnteredAutomation([
    'date' => '2025-03-15',
    'page' => 1,
    'perPage' => 50,
]);
```

---

## Subscribers — `$client->subscribers()`

### `statusSubscriber(string $email)`

```php
$status = $client->subscribers()->statusSubscriber('user@example.com');
```

### `unsubscribedEmails(string $date_from, string $date_to)`

```php
$emails = $client->subscribers()->unsubscribedEmails('2025-01-01', '2025-01-31');
```

Alte metode (add, remove, tags, bulk etc.): vezi `src/Api/SubscribersApi.php` și DTO-urile din `src/DTO/Subscribers/`.

---

## Orders — `$client->orders()`

### `saveOrder(array $payload)`

Payload-ul este validat de `SaveOrder` — multe câmpuri obligatorii (comandă, client, produse). Consultă `src/DTO/Orders/SaveOrder.php` sau testele din `tests/OrdersApiTest.php`.

```php
$result = $client->orders()->saveOrder([
    // 'number', 'email_address', 'phone', 'firstname', 'lastname', …
]);
```

### `updateOrderStatus(string $order_number, string $order_status)`

```php
$result = $client->orders()->updateOrderStatus('ORD-1001', 'completed');
```

---

## Transactionale — `$client->transactionals()`

### `sendEmail(array $payload)`

```php
$result = $client->transactionals()->sendEmail([
    'to' => 'recipient@example.com',
    'subject' => 'Titlu',
    'body' => '<p>Conținut HTML</p>',
    'from' => 'Sender Name <shop@example.com>', // opțional
    'reply_to' => 'support@example.com',        // opțional
]);
```

### `sendSms(string $to, string $content)`

```php
$result = $client->transactionals()->sendSms('+40700000000', 'Mesaj scurt');
```

---

## Produse — `$client->products()`

### `createProduct(array $payload)` / `updateProduct(array $payload)`

DTO-urile impun câmpuri precum `id`, `sku`, `name`, `price`, etc. Vezi `src/DTO/Products/CreateProduct.php`.

```php
$result = $client->products()->createProduct([
    'id' => 100,
    'sku' => 'SKU-1',
    'name' => 'Produs',
    // …
]);
```

---

## Campanii — `$client->campaigns()`

### `list()`

```php
$campaigns = $client->campaigns()->list();
```

### `create(array $payload)`

Structură complexă: `type`, `mode`, `sender`, `audience`, `subject`, `content`, `scheduling`, `tracking`. Vezi `src/DTO/Campaigns/CreateCampaign.php` și `tests/CampaignsApiTest.php` (`minimalCreateCampaignPayload`).

```php
$result = $client->campaigns()->create([
    'type' => 'email',
    'mode' => 'regular',
    'sender' => [
        'sender_name' => 'Magazin',
        'sender_email' => 'shop@example.com',
        'reply_to' => 'support@example.com',
    ],
    // … audience, subject, content, scheduling, tracking
]);
```

### `getEmailReport(string|int $id)`

```php
$report = $client->campaigns()->getEmailReport('campaign-id');
```

### `getLatestCampaign(?int $limit = null)`

```php
$latest = $client->campaigns()->getLatestCampaign(5);
```

---

## Loialitate — `$client->loyalty()`

### `getInfo(string $email)`

```php
$info = $client->loyalty()->getInfo('user@example.com');
```

### `managePoints(string $email, string $action, int $points)`

```php
$result = $client->loyalty()->managePoints('user@example.com', 'increase', 100);
```

---

## Cupoane — `$client->coupons()`

### `getAvailableCoupons(string $email)`

```php
$coupons = $client->coupons()->getAvailableCoupons('user@example.com');
```

### `save(array $payload)`

```php
$result = $client->coupons()->save([
    'code' => 'SUMMER10',
    'type' => 'your-type', // string — valorile acceptate sunt definite de API
    'value' => '10',
    'expiration_date' => '2025-12-31',
]);
```

---

## Recenzii — `$client->reviews()`

### `get(array $query = [])`

Parametri opționali: `page`, `perPage`, `t` (vezi `ProductReviewsQuery`).

```php
$reviews = $client->reviews()->get(['page' => 1, 'perPage' => 20]);
```

### `create(array $payload)`

```php
$result = $client->reviews()->create([
    'order_id' => 'ORD-1',
    'review_date' => '2025-03-01',
    'order_rating' => '5',
]);
```

---

## App push — `$client->appPush()`

### `setToken(string $email, string $token, string $type)`

`type`: de ex. `ios` sau `android` (conform validării din DTO).

```php
$result = $client->appPush()->setToken(
    'user@example.com',
    'fcm-device-token…',
    'android',
);
```

### `removeToken(string $email, string $type)`

```php
$result = $client->appPush()->removeToken('user@example.com', 'ios');
```

---

## Evenimente — `$client->events()`

### `sendCustomEvent(array $payload)`

```php
$result = $client->events()->sendCustomEvent([
    'email' => 'user@example.com',
    'event' => 'viewed_product',
]);
```

---

## Rapoarte — `$client->reports()`

Query-urile includ de obicei interval `start` / `end` (date) și `type` (în funcție de endpoint). Pentru rapoarte email, `type` poate fi valorile din `TheMarketer\ApiClient\Enum\EmailReportType` (ex. `sent`, `open-rate`).

### `getEmailCampaigns(array $query)`

```php
$result = $client->reports()->getEmailCampaigns([
    'type' => 'sent',
    'start' => '2025-01-01',
    'end' => '2025-01-31',
]);
```

Alte metode pe același API: `getEmailAutomation`, `getSmsCampaigns`, `getPushCampaigns`, `getAudience`, `getForms` — vezi `src/Api/ReportsApi.php` și DTO-urile din `src/DTO/Reports/`.

---

## Teste

```bash
composer test
```

---

## Script de verificare rapidă

În repo există `smoke.php` (necesită chei și URL reale). Rulezi din rădăcina proiectului:

```bash
php smoke.php
```
