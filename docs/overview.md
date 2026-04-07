# Overview

`themarketer/api-client` este clientul oficial PHP pentru integrarea cu The Marketer API.

## What this package does

- Trimite request-uri HTTP catre endpoint-urile The Marketer.
- Valideaza payload-urile local prin DTO-uri.
- Returneaza raspunsuri JSON decodate pentru majoritatea metodelor.
- Mapeaza erorile HTTP in exceptii clare.

## Requirements

- PHP `^8.1`
- Composer
- `ext-mbstring`

## Core usage model

1. Creezi un `Client` cu `customerId` si `restKey`.
2. Apelezi un modul (de exemplu `orders()`).
3. Trimiteri payload valid catre metoda dorita.
4. Procesezi raspunsul sau tratezi exceptiile.

## Available API modules

- `subscribers()`
- `orders()`
- `transactionals()`
- `products()`
- `campaigns()`
- `loyalty()`
- `coupons()`
- `reviews()`
- `appPush()`
- `events()`
- `reports()`

## Next step

Continua cu [Quickstart](./quickstart.md) pentru primul request functional.
