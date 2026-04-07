# Orders

Acest modul acopera operatiunile uzuale legate de comenzi.

## Access the module

```php
$ordersApi = $client->orders();
```

## Save order

```php
$payload = [
    'order_no' => '1001',
    'email' => 'john@doe.com',
    'products' => [
        [
            'sku' => 'SKU-123',
            'quantity' => 1,
            'price' => 99.99,
        ],
    ],
];

$result = $ordersApi->saveOrder($payload);
```

## Save order alias

In unele zone ai aliasuri de metoda pentru ergonomie:

```php
$result = $ordersApi->save($payload);
```

## Recommended payload quality

- `order_no` unic per comanda.
- Email valid in `email`.
- Lista de `products` nevida.
- Pentru fiecare produs: SKU, cantitate si pret corecte.

## Validation and error flow

1. DTO-ul valideaza local campurile.
2. Daca validarea pica, apare `ValidationException`.
3. Daca API raspunde cu eroare, apare exceptia mapata de client.

## Best practices

- Logheaza payload minim + raspunsul API (fara date sensibile).
- Foloseste idempotenta in apeluri duplicate din job-uri/retry.
- Ruleaza teste de integrare cu payload-uri reale in mediu non-production.
