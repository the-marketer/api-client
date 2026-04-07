# Errors and Troubleshooting

Ghid rapid pentru cele mai comune erori.

## Exception mapping

| HTTP status | Exception |
| --- | --- |
| `401` | `UnauthorizedException` |
| `404` | `CustomerNotFoundException` |
| `405` | `MethodNotAllowedException` |
| Other API errors | `ApiException` |

Erorile de validare locala (DTO/config) apar ca `ValidationException`.

## Common scenarios

### 401 Unauthorized

**Cauza:** `customerId` sau `restKey` invalid.

**Fix:**
- verifica credentialele active;
- ruleaza `checkApiCredentials()` la bootstrap.

### 404 Customer not found

**Cauza:** customer ID inexistent sau endpoint gresit.

**Fix:**
- verifica `customerId`;
- confirma mediul/host-ul configurat.

### 405 Method not allowed

**Cauza:** endpoint corect, metoda HTTP incorecta.

**Fix:**
- verifica metoda API folosita de clientul curent.

### JsonException

**Cauza:** raspuns invalid JSON la endpoint-ul apelat.

**Fix:**
- logheaza raspunsul brut;
- verifica eventuale probleme temporare pe API upstream.

## Troubleshooting checklist

- Ruleaza `checkApiCredentials()` inainte de apeluri business.
- Activeaza logging pentru request/response metadata.
- Valideaza payload-ul inainte de trimitere.
- Izoleaza problema cu un payload minim.
