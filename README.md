# The Marketer API Client (PHP)

Client PHP pentru API-ul **The Marketer**. Trimite cereri HTTP prin **Guzzle** (clasa internă `ApiGateway`) și validează payload-urile cu **Symfony Validator** (DTO-uri `AbstractPayload` / `Data` din `TheMarketer\ApiClient\Common`).

## Documentatie pentru clienti

Pentru o varianta structurata, usor de parcurs:

- [Documentation index](./docs/README.md)
- [Overview](./docs/overview.md)
- [Quickstart](./docs/quickstart.md)
- [Authentication](./docs/authentication.md)
- [Orders](./docs/orders.md)
- [Errors and Troubleshooting](./docs/errors.md)

### Docusaurus site (GitHub Pages)

Documentatia este pregatita si ca site Docusaurus in folderul `website/`.

Rulare locala:

```bash
cd website
npm install
npm run start
```

Deploy pe GitHub Pages este configurat prin workflow-ul:

- `.github/workflows/deploy-docs.yml`

## Cerințe (requirements)

| Cerință | Versiune / notă |
|--------|------------------|
| **PHP** | `^8.1` |
| **Composer** | pentru instalarea dependențelor |
| **ext-mbstring** | obligatoriu |
| **Dependențe principale** | `guzzlehttp/guzzle` ^7, `symfony/validator` ^7, `symfony/expression-language` ^8 |
| **Dezvoltare / teste** | `phpunit` ^10, `orchestra/testbench` ^8 (pentru suite-ul de teste) |

Instalare în proiect:

```bash
composer install
```

Pachet public (exemplu):

```bash
composer require themarketer/api-client
```

---

## Arhitectură pe scurt

- **`TheMarketer\ApiClient\Client`** — punctul unic de intrare: primește `customerId` și `restKey`, construiește **`ApiContext`** + **`ApiGateway`** (Guzzle cu retry opțional) și expune metode pentru fiecare zonă API (`subscribers()`, `orders()`, `checkCredentials()`, etc.).
- **`ApiGateway`** — adaugă în query autentificarea `k` (rest key) și `u` (customer id), trimite JSON la POST acolo unde DTO-ul o cere, mapează erorile HTTP la excepții (`UnauthorizedException`, `ApiException`, …).
- **Clasele din `src/Api/`** (`SubscribersApi`, `OrdersApi`, …) — încapsulează endpoint-urile; validarea inputului se face în **`src/DTO/`**.

Baza URL pentru cereri este `Config::baseUrl()` = `{apiUrl}/api/{version}` (implicit `apiVersion` = `v1`). Constructorul `Client` folosește `new Config($customerId, $restKey)` — dacă ai nevoie de alt host decât cel din `Config` (ex. producție), trebuie fie extins clientul, fie modificat `Config` în sursă; verifică valoarea implicită a `apiUrl` în `src/Common/Config.php` pentru mediul tău.

---

## Utilizare de bază

```php
use TheMarketer\ApiClient\Client;

$client = new Client(
    customerId: 'ID_CONT_THE_MARKETER',  // devine query `u`
    restKey: 'CHEIA_REST',                 // devine query `k`
    maxRetryAttempts: 1,                   // opțional: reîncercări la erori tranzitorii (0 = fără retry)
);

// Exemple de acces la API-uri grupate
$client->subscribers()->addSubscriber([/* … */]);
$client->orders()->saveOrder([/* … */]);
$client->transactionals()->sendEmail([/* … */]);
```

Aliasuri utile: `add()` → `addSubscriber()`, `save()` → `saveOrder()` (unde există).

---

## Credențiale și utilitare (direct pe `Client`)

Aceste metode nu trec prin `subscribers()` / `orders()`; sunt delegate la **`CredentialsClient`** intern.

### `checkCredentials(string $trackingKey): array`

Verifică credențialele: tracking key-ul este trimis în **body JSON** (împreună cu `r` și `u` derivate din config), pe lângă query-ul `k`/`u` standard.

```php
$result = $client->checkCredentials('CHEIE_TRACKING');
// ex. ['success' => true] — JSON decodat de la API
```

### `checkApiCredentials(): array`

POST către endpoint-ul de verificare API; răspuns JSON decodat ca `array`.

```php
$result = $client->checkApiCredentials();
```

### `getCosts()`, `getRealtimeVisitors()`, `getSmsCredit(): array`

Răspuns JSON decodat.

### `getReferralLink(?string $email = null): string`

Returnează **conținutul brut** al răspunsului (nu JSON).

### `getDeliveryLogs(array $payload): array`

`email` obligatoriu; opțional: `per_page`, `page`, `start`, `end`.

### `getEnteredAutomation(array $payload): array`

`date` obligatoriu (`Y-m-d`); opțional `page`, `perPage`.

### `config(): Config`

Acces la `customerId`, `restKey`, `baseUrl()` după nevoie.

---

## Module API (exemple)

| Accesor pe `Client` | Rol |
|---------------------|-----|
| `subscribers()` | Abonați: status, add/remove, bulk, tag-uri, etc. |
| `orders()` | Comenzi, feed URL, retail, statistici |
| `transactionals()` | Email și SMS tranzacționale |
| `products()` | CRUD / sync produse, categorii, branduri |
| `campaigns()` | Listă, creare campanie, raport email, ultima campanie |
| `loyalty()` | Puncte loialitate |
| `coupons()` | Cupoane disponibile, salvare |
| `reviews()` | Recenzii produse, merchant, setări Merchant Pro |
| `mobilePush()` | Token-uri push (mobile) |
| `events()` | Evenimente personalizate |
| `reports()` | Rapoarte email/SMS/push/forms/audience |

Detalii despre parametri: fișierele din `src/Api/*Api.php` și `src/DTO/**`. Testele din `tests/*ApiTest.php` arată exemple de payload-uri valide.

### Campanii — note importante

- **`list()`** folosește **POST** către `/campaigns/list`, cu body din `ListCampaign`.
- **`create()`** cere structură imbricată validată de `CreateCampaign`; la **sender** folosește cheile **`name`**, **`sender`** (email), **`reply_to`** (nu `sender_name` / `sender_email`).

### Recenzii

- **`get()`** returnează **string** (conținut răspuns, nu `array` decodat automat).

### Rapoarte

- Query-urile includ de obicei `start`, `end`, `type` (vezi enum-urile din `src/Enum/` și DTO-urile din `src/DTO/Reports/`).

---

## Erori și excepții

**Înainte de request (validare locală)**

- **`TheMarketer\ApiClient\Exception\ValidationException`** — lipsă `customerId` / `restKey` în config sau mesaje din validarea Symfony pe DTO.
- Lipsă argumente obligatorii la construirea DTO poate duce la **`ArgumentCountError`** sau **`TypeError`** înainte de rețea.

**După request** (`ApiGateway` mapează status HTTP)

| Status | Excepție |
|--------|----------|
| **401** | `UnauthorizedException` |
| **404** | `CustomerNotFoundException` |
| **405** | `MethodNotAllowedException` |
| Alte erori | `ApiException` (folosește codul și mesajul din răspuns; mesajul e extras din JSON `message` când există) |

La răspuns de succes cu JSON invalid, metodele care decodă pot arunca **`JsonException`**. Pentru erori de rețea: **`GuzzleHttp\Exception\GuzzleException`**.

---

## Teste

```bash
composer test
```

Suite-ul folosește `ApiGateway` cu **Guzzle MockHandler** (fără apeluri reale la API). Pentru **`Client`** în teste cu mock HTTP, `context` este `readonly`; în practică se testează `CredentialsClient` și clasele `*Api` cu același stack — vezi `tests/CredentialsClientTest.php` și `tests/TestCase.php`.

---

## Script smoke (`smoke.php`)

Verificare rapidă cu **credențiale reale** (nu comita chei în repo):

```bash
php smoke.php
```

Exemplul din repo apelează `checkCredentials($trackingKey)` — înlocuiește valorile cu cele din contul tău The Marketer.

---

## Licență

MIT (vezi `composer.json`).
