---
sidebar_position: 2
title: Overview
---

`themarketer/api-client` is the official PHP client for The Marketer API.

## What this package does

- Sends HTTP requests to The Marketer endpoints.
- Validates payloads locally with DTOs.
- Returns decoded JSON for most methods.
- Maps HTTP failures to explicit exceptions.

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
