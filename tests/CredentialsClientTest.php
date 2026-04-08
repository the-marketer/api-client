<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use NotificationService\Sdk\Internal\CredentialsClient;
use TheMarketer\ApiClient\Common\ApiContext;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\Gateways\ApiGateway;

final class CredentialsClientTest extends TestCase
{
    /**
     * @return array{0: CredentialsClient, 1: \stdClass}
     */
    private function clientWithMockResponses(Response ...$responses): array
    {
        return $this->createApiWithMock(CredentialsClient::class, ...$responses);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testCheckCredentialsSendsPostWithKruInJsonBodyAndAuthQuery(): void
    {
        [$client, $container] = $this->clientWithMockResponses(
            new Response(200, [], '{"success":true}'),
        );

        $result = $client->checkCredentials('tracking-key-xyz');

        $this->assertSame(['success' => true], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/check-credentials', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::MOCK_API_KEY, $query['k']);
        $this->assertSame(self::MOCK_DOMAIN, $query['u']);

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame([
            'k' => 'tracking-key-xyz',
            'r' => self::MOCK_API_KEY,
            'u' => self::MOCK_DOMAIN,
        ], $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testCheckApiCredentialsSendsPostWithoutJsonBody(): void
    {
        [$client, $container] = $this->clientWithMockResponses(
            new Response(200, [], '{"valid":true}'),
        );

        $result = $client->checkApiCredentials();

        $this->assertSame(['valid' => true], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/check-api-credentials', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::MOCK_API_KEY, $query['k']);
        $this->assertSame(self::MOCK_DOMAIN, $query['u']);

        $this->assertSame('', (string) $request->getBody());
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetCostsSendsGetRequestAndReturnsDecodedJson(): void
    {
        [$client, $container] = $this->clientWithMockResponses(
            new Response(200, [], '{"total":42,"currency":"EUR"}'),
        );

        $result = $client->getCosts();

        $this->assertSame(['total' => 42, 'currency' => 'EUR'], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/get_costs', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::MOCK_API_KEY, $query['k']);
        $this->assertSame(self::MOCK_DOMAIN, $query['u']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetRealtimeVisitorsSendsGetRequestAndReturnsDecodedJson(): void
    {
        [$client, $container] = $this->clientWithMockResponses(
            new Response(200, [], '{"visitors":3}'),
        );

        $result = $client->getRealtimeVisitors();

        $this->assertSame(['visitors' => 3], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/realtime_visitors', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::MOCK_API_KEY, $query['k']);
        $this->assertSame(self::MOCK_DOMAIN, $query['u']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetSmsCreditSendsGetRequestAndReturnsDecodedJson(): void
    {
        [$client, $container] = $this->clientWithMockResponses(
            new Response(200, [], '{"credit":100}'),
        );

        $result = $client->getSmsCredit();

        $this->assertSame(['credit' => 100], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/check-sms-credit', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::MOCK_API_KEY, $query['k']);
        $this->assertSame(self::MOCK_DOMAIN, $query['u']);
    }

    /**
     * @throws GuzzleException
     */
    public function testGetReferralLinkWithoutEmailReturnsResponseBody(): void
    {
        [$client, $container] = $this->clientWithMockResponses(
            new Response(200, [], 'https://ref.example.test/abc'),
        );

        $result = $client->getReferralLink();

        $this->assertSame('https://ref.example.test/abc', $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/get-referral-link', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::MOCK_API_KEY, $query['k']);
        $this->assertSame(self::MOCK_DOMAIN, $query['u']);
    }

    /**
     * @throws GuzzleException
     */
    public function testGetReferralLinkWithEmailAddsEmailQuery(): void
    {
        [$client, $container] = $this->clientWithMockResponses(
            new Response(200, [], 'https://ref.example.test/u'),
        );

        $result = $client->getReferralLink('user@gmail.com');

        $this->assertSame('https://ref.example.test/u', $result);

        $request = $this->lastRequest($container);
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame('user@gmail.com', $query['email']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetDeliveryLogsSendsGetWithEmailQuery(): void
    {
        [$client, $container] = $this->clientWithMockResponses(
            new Response(200, [], '{"logs":[]}'),
        );

        $result = $client->getDeliveryLogs(['email' => 'user@gmail.com']);

        $this->assertSame(['logs' => []], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/delivery-logs', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame('user@gmail.com', $query['email']);
        $this->assertSame(self::MOCK_API_KEY, $query['k']);
        $this->assertSame(self::MOCK_DOMAIN, $query['u']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetDeliveryLogsIncludesOptionalQueryParameters(): void
    {
        [$client, $container] = $this->clientWithMockResponses(
            new Response(200, [], '{}'),
        );

        $client->getDeliveryLogs([
            'email' => 'user@gmail.com',
            'per_page' => 25,
            'page' => 2,
            'start' => '2025-01-01',
            'end' => '2025-03-01',
        ]);

        $request = $this->lastRequest($container);
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame('25', $query['per_page']);
        $this->assertSame('2', $query['page']);
        $this->assertSame('2025-01-01', $query['start']);
        $this->assertSame('2025-03-01', $query['end']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetEnteredAutomationSendsGetWithDateQuery(): void
    {
        [$client, $container] = $this->clientWithMockResponses(
            new Response(200, [], '{"items":[]}'),
        );

        $result = $client->getEnteredAutomation(['date' => '2025-03-15']);

        $this->assertSame(['items' => []], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/entered-automation', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame('2025-03-15', $query['date']);
        $this->assertSame(self::MOCK_API_KEY, $query['k']);
        $this->assertSame(self::MOCK_DOMAIN, $query['u']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetEnteredAutomationIncludesOptionalPageAndPerPage(): void
    {
        [$client, $container] = $this->clientWithMockResponses(
            new Response(200, [], '{}'),
        );

        $client->getEnteredAutomation([
            'date' => '2025-03-15',
            'page' => 3,
            'perPage' => 50,
        ]);

        $request = $this->lastRequest($container);
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame('3', $query['page']);
        $this->assertSame('50', $query['perPage']);
    }

    public function testGetCostsThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $config = new Config('', self::MOCK_API_KEY, self::MOCK_BASE_URL);
        $api = new CredentialsClient(new ApiContext(new ApiGateway($config, 0, $client), $config));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->getCosts();
    }

    public function testGetCostsThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $config = new Config(self::MOCK_DOMAIN, '', self::MOCK_BASE_URL);
        $api = new CredentialsClient(new ApiContext(new ApiGateway($config, 0, $client), $config));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->getCosts();
    }

    public function testCheckCredentialsThrowsWhenTrackingKeyMissing(): void
    {
        [$client] = $this->clientWithMockResponses();

        $this->expectException(ValidationException::class);

        $client->checkCredentials('');
    }

    public function testGetDeliveryLogsThrowsWhenEmailInvalid(): void
    {
        [$client] = $this->clientWithMockResponses();

        $this->expectException(ValidationException::class);

        $client->getDeliveryLogs(['email' => 'not-an-email']);
    }

    public function testGetEnteredAutomationThrowsWhenDateInvalid(): void
    {
        [$client] = $this->clientWithMockResponses();

        $this->expectException(ValidationException::class);

        $client->getEnteredAutomation(['date' => '15-03-2025']);
    }
}
