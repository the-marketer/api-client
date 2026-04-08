# Errors and Troubleshooting

Ghid rapid pentru cele mai comune erori.

## Exception mapping

| HTTP status | Exception |
| --- | --- |
| `401` | `UnauthorizedException` |
| `404` | `CustomerNotFoundException` |
| `405` | `MethodNotAllowedException` |
| Other API errors | `ApiException` |

Erorile de validare locală (DTO/config) apar ca `TheMarketer\ApiClient\Exception\ValidationException`.

Înainte de request, gateway-urile verifică și configurația:

- **REST** (`ApiGateway`): `customerId` și `restKey` ne-goale.
- **Tracking** (`TrackingGateway`): `trackingKey` ne-gol pentru fluxuri pe host-ul de tracking.

## Common scenarios

### 401 Unauthorized

**Cauză:** `customerId` sau `restKey` invalid.

**Fix:**
- verifică credențialele active;
- rulează `checkApiCredentials()` la bootstrap (returnează `bool` pe `Client`).

### 404 Customer not found

**Cauză:** customer ID inexistent sau endpoint greșit.

**Fix:**
- verifică `customerId`;
- confirmă mediul/host-ul configurat.

### 405 Method not allowed

**Cauză:** endpoint corect, metodă HTTP incorectă.

**Fix:**
- verifică metoda API folosită de clientul curent.

### ValidationException (config)

**Cauză:** lipsă `trackingKey` la apeluri care folosesc gateway-ul de tracking.

**Fix:** adaugă `trackingKey` în array-ul pasat la `new Client([...])`.

### JsonException

**Cauză:** răspuns invalid JSON la endpoint-ul apelat.

**Fix:**
- loghează răspunsul brut;
- verifică eventuale probleme temporare pe API upstream.

## Troubleshooting checklist

- Rulează `checkApiCredentials()` înainte de apeluri business.
- Activează logging pentru request/response metadata.
- Validează payload-ul înainte de trimitere.
- Izolează problema cu un payload minim.
