<?php

declare(strict_types=1);

namespace Tests;

use TheMarketer\ApiClient\Client;

/**
 * Teste de suprafață pe {@see Client}. Delegarea către {@see CredentialsClient} pentru metodele de credențiale este acoperită în {@see ClientCredentialsFacadeTest}.
 */
final class ClientTest extends TestCase
{
    public function testConfigExposesCustomerIdAndRestKey(): void
    {
        $client = new Client([
            'customerId' => 'customer-99',
            'restKey' => 'rest-secret-1',
        ]);

        $this->assertSame('customer-99', $client->config()->customerId());
        $this->assertSame('rest-secret-1', $client->config()->restKey());
    }
}
