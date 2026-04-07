<?php

declare(strict_types=1);

namespace Tests;

use TheMarketer\ApiClient\Client;

/**
 * Teste de suprafață pe {@see Client}. Comportamentul HTTP pentru credențiale este acoperit în {@see CredentialsClientTest}.
 *
 * Pentru a testa `checkCredentials`, `getCosts` etc. direct pe `Client` cu Guzzle mock, ar fi nevoie fie de injectare de dependențe în constructorul `Client`,
 * fie de un factory de test — `context` este `private readonly` și nu poate fi înlocuit după construire.
 */
final class ClientTest extends TestCase
{
    public function testConfigExposesCustomerIdAndRestKey(): void
    {
        $client = new Client('customer-99', 'rest-secret-1');

        $this->assertSame('customer-99', $client->config()->customerId());
        $this->assertSame('rest-secret-1', $client->config()->restKey());
    }
}
