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

- **`TheMarketer\ApiClient\Client`** — punct unic de intrare: primește un **array** de configurare (`customerId`, `restKey`, opțional `trackingKey`, `restUrl`, `trackingUrl`, `maxRetryAttempts`), construiește **`Config`** + **`ApiContext`** și expune modulele API (`subscribers()`, `orders()`, `checkCredentials()`, etc.).
- **`ApiContext`** — oferă gateway **REST** (`ApiGateway`) și **tracking** (`TrackingGateway`); ambele folosesc Guzzle (retry configurabil).
- **`ApiGateway`** — pentru REST: query `k` (rest key) și `u` (customer id); JSON la POST unde cere DTO-ul; mapează erorile HTTP la excepții.
- **`TrackingGateway`** — pentru host-ul de tracking: necesită `trackingKey` în config; alt set de parametri de autentificare în query.
- **Clasele din `src/Api/`** — încapsulează endpoint-urile; validarea inputului se face în **`src/DTO/`**.

Baza URL pentru REST este `Config::baseRestUrl()` = `{restUrl}/api/{apiVersion}/` (implicit `apiVersion` = `v1`). URL-urile implicite sunt în `Client` / `Config` (`src/Common/Config.php`).

---

## Utilizare de bază

```php
use TheMarketer\ApiClient\Client;

$client = new Client([
    'customerId' => 'ID_CONT_THE_MARKETER', // query `u` pe REST
    'restKey' => 'CHEIA_REST',               // query `k` pe REST
    'trackingKey' => 'CHEIE_TRACKING',     // opțional: pentru evenimente tracking
    'maxRetryAttempts' => 1,                // opțional
]);

// Exemple de acces la API-uri grupate
$client->subscribers()->addSubscriber([/* … */]);
$client->orders()->saveOrder([/* … */]);
$client->transactionals()->sendEmail([/* … */]);
```

---

## Credențiale și utilitare (direct pe `Client`)

Aceste metode nu trec prin `subscribers()` / `orders()`; sunt delegate la **`CredentialsClient`** intern.

### `checkCredentials(string $trackingKey): bool`

Verifică credențialele (tracking key în **body JSON** pe REST, vezi `CredentialsClient`). Pe **`Client`**, returnează **`true`** dacă răspunsul decodat este array gol `[]`, altfel **`false`**. Pentru JSON-ul brut ca `array`, folosește `CredentialsClient::checkCredentials()` cu același context.

```php
$ok = $client->checkCredentials('CHEIE_TRACKING');
```

### `checkApiCredentials(): bool`

Pe **`Client`**, returnează **`bool`** (același criteriu: corp JSON → array gol = succes). Pentru răspuns decodat complet, vezi `CredentialsClient::checkApiCredentials()`.

```php
$ok = $client->checkApiCredentials();
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

Acces la `customerId`, `restKey`, `baseRestUrl()`, `trackingKey()`, etc.

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
| `mobilePush()` | Push mobil (token-uri iOS/Android) |
| `events()` | Evenimente personalizate |
| `reports()` | Rapoarte email/SMS/push/forms/audience |

Detalii despre parametri: fișierele din `src/Api/*Api.php` și `src/DTO/**`. Testele din `tests/*ApiTest.php` arată exemple de payload-uri valide.

### Campanii — note importante

- **`list()`** folosește **POST** către `/campaigns/list`, cu body din `ListCampaign`.
- **`create()`** cere structură imbricată validată de `CreateCampaign`; la **sender** folosește cheile **`name`**, **`sender`** (email), **`reply_to`** (nu `sender_name` / `sender_email`).

### Recenzii

- **`getProductReviews()`** returnează **string** (conținut răspuns, nu `array` decodat automat).

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
