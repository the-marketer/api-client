---
sidebar_position: 6
title: Errors and Troubleshooting
---

## Exception mapping

| HTTP status | Exception |
| --- | --- |
| `401` | `UnauthorizedException` |
| `404` | `CustomerNotFoundException` |
| `405` | `MethodNotAllowedException` |
| Other API errors | `ApiException` |

Local payload/config validation failures are raised as `ValidationException`.

## Fast troubleshooting checklist

- Run `checkApiCredentials()` before business calls.
- Log request/response metadata.
- Validate payload shape before sending.
- Reproduce with a minimal payload.
