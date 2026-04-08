<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Psr7\Response;
use NotificationService\Sdk\Internal\CredentialsClient;
use ReflectionProperty;
use TheMarketer\ApiClient\Client;

/**
 * Verifică delegarea din {@see Client} către {@see CredentialsClient} folosind un client HTTP mock (fără dubluri PHPUnit pe clasă finală).
 */
final class ClientCredentialsFacadeTest extends TestCase
{
    /**
     * @return array{0: Client, 1: \stdClass}
     */
    private function clientUsingCredentialsClient(CredentialsClient $credentials): array
    {
        $client = new Client([
            'customerId' => self::MOCK_DOMAIN,
            'restKey' => self::MOCK_API_KEY,
        ]);

        $prop = new ReflectionProperty(Client::class, 'credentials');
        $prop->setAccessible(true);
        $prop->setValue($client, $credentials);

        return [$client];
    }

    public function testCheckCredentialsReturnsTrueWhenApiReturnsEmptyJsonArray(): void
    {
        [$credentials, $bucket] = $this->createApiWithMock(CredentialsClient::class, new Response(200, [], '[]'));
        [$client] = $this->clientUsingCredentialsClient($credentials);

        $this->assertTrue($client->checkCredentials('tracking-key-xyz'));

        $request = $this->lastRequest($bucket);
        $this->assertStringEndsWith('/check-credentials', $request->getUri()->getPath());
    }

    public function testCheckCredentialsReturnsFalseWhenApiReturnsNonEmptyJson(): void
    {
        [$credentials, $bucket] = $this->createApiWithMock(
            CredentialsClient::class,
            new Response(200, [], '{"errors":["bad"]}'),
        );
        [$client] = $this->clientUsingCredentialsClient($credentials);

        $this->assertFalse($client->checkCredentials('tracking-key-xyz'));
    }

    public function testCheckApiCredentialsReturnsTrueWhenApiReturnsEmptyArray(): void
    {
        [$credentials, $bucket] = $this->createApiWithMock(CredentialsClient::class, new Response(200, [], '[]'));
        [$client] = $this->clientUsingCredentialsClient($credentials);

        $this->assertTrue($client->checkApiCredentials());

        $request = $this->lastRequest($bucket);
        $this->assertStringEndsWith('/check-api-credentials', $request->getUri()->getPath());
    }

    public function testCheckApiCredentialsReturnsFalseWhenApiReturnsNonEmptyJson(): void
    {
        [$credentials, $bucket] = $this->createApiWithMock(
            CredentialsClient::class,
            new Response(200, [], '{"valid":false}'),
        );
        [$client] = $this->clientUsingCredentialsClient($credentials);

        $this->assertFalse($client->checkApiCredentials());
    }

    public function testGetCostsReturnsDecodedResponse(): void
    {
        [$credentials, $bucket] = $this->createApiWithMock(
            CredentialsClient::class,
            new Response(200, [], '{"total":42,"currency":"EUR"}'),
        );
        [$client] = $this->clientUsingCredentialsClient($credentials);

        $this->assertSame(['total' => 42, 'currency' => 'EUR'], $client->getCosts());

        $request = $this->lastRequest($bucket);
        $this->assertStringEndsWith('/get_costs', $request->getUri()->getPath());
    }

    public function testGetRealtimeVisitorsReturnsDecodedResponse(): void
    {
        [$credentials, $bucket] = $this->createApiWithMock(
            CredentialsClient::class,
            new Response(200, [], '{"visitors":3}'),
        );
        [$client] = $this->clientUsingCredentialsClient($credentials);

        $this->assertSame(['visitors' => 3], $client->getRealtimeVisitors());

        $request = $this->lastRequest($bucket);
        $this->assertStringEndsWith('/realtime_visitors', $request->getUri()->getPath());
    }

    public function testGetSmsCreditReturnsDecodedResponse(): void
    {
        [$credentials, $bucket] = $this->createApiWithMock(
            CredentialsClient::class,
            new Response(200, [], '{"credit":100}'),
        );
        [$client] = $this->clientUsingCredentialsClient($credentials);

        $this->assertSame(['credit' => 100], $client->getSmsCredit());

        $request = $this->lastRequest($bucket);
        $this->assertStringEndsWith('/check-sms-credit', $request->getUri()->getPath());
    }

    public function testGetReferralLinkReturnsResponseBodyString(): void
    {
        [$credentials, $bucket] = $this->createApiWithMock(
            CredentialsClient::class,
            new Response(200, [], 'https://ref.example.test/abc'),
        );
        [$client] = $this->clientUsingCredentialsClient($credentials);

        $this->assertSame('https://ref.example.test/abc', $client->getReferralLink());

        $request = $this->lastRequest($bucket);
        $this->assertStringEndsWith('/get-referral-link', $request->getUri()->getPath());
    }

    public function testGetDeliveryLogsReturnsDecodedResponse(): void
    {
        [$credentials, $bucket] = $this->createApiWithMock(
            CredentialsClient::class,
            new Response(200, [], '{"logs":[]}'),
        );
        [$client] = $this->clientUsingCredentialsClient($credentials);

        $this->assertSame(['logs' => []], $client->getDeliveryLogs(['email' => 'user@gmail.com']));

        $request = $this->lastRequest($bucket);
        $this->assertStringEndsWith('/delivery-logs', $request->getUri()->getPath());
    }

    public function testGetEnteredAutomationReturnsDecodedResponse(): void
    {
        [$credentials, $bucket] = $this->createApiWithMock(
            CredentialsClient::class,
            new Response(200, [], '{"items":[]}'),
        );
        [$client] = $this->clientUsingCredentialsClient($credentials);

        $this->assertSame(['items' => []], $client->getEnteredAutomation(['date' => '2025-03-15']));

        $request = $this->lastRequest($bucket);
        $this->assertStringEndsWith('/entered-automation', $request->getUri()->getPath());
    }
}
