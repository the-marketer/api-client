---
sidebar_position: 2
title: Overview
---

`themarketer/api-client` is the official PHP client for The Marketer API.

## What this package does

- Consumes The Marketer API from PHP applications through a clean, module-based client.
- Helps you integrate faster by keeping request payloads consistent and validated before sending.
- Provides a predictable integration flow: authenticate, call a module, send data, receive a response.
- Surfaces API failures in a clear way, so you can handle errors reliably in your code.

## Requirements

- PHP `^8.1`
- Composer
- `ext-mbstring`

## Core usage model

1. Create a `Client` using `customerId` and `restKey`.
2. Access a module (for example `orders()`).
3. Send a validated payload.
4. Handle response data or exceptions.

## Available API modules

- [`orders()`](./orders.md)
- [`subscribers()`](./subscribers.md)
- [`campaigns()`](./campaigns.md)
- [`products()`](./products.md)
- [`transactionals()`](./transactionals.md)
- [`reports()`](./reports.md)
- [`events()`](./events.md)
- [`coupons()`](./coupons.md)
- [`loyalty()`](./loyalty.md)
- [`reviews()`](./reviews.md)
- [`appPush()`](./app-push.md)

Utilities:

- [`Client` utilities](./credentials-utilities.md)
- [Errors & troubleshooting](./errors.md)
