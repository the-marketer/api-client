<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Psr\Http\Message\RequestInterface;
use TheMarketer\ApiClient\CredentialsClient;
use TheMarketer\ApiClient\Exception\ValidationException;

final class CredentialsClientTest extends TestCase
{
    private const BASE_URL = 'https://api.example.test';

    private const DOMAIN_KEY = 'domain-1';

    private const API_KEY = 'api-secret';

    /**
     * @return array{0: CredentialsClient, 1: \stdClass}
     *
     * @phpstan-param \stdClass&object{requests: list<RequestInterface>} $bucket
     */
    private function clientWithMockResponses(Response ...$responses): array
    {
        $bucket = new \stdClass();
        $bucket->requests = [];

        $queue = [];
        foreach ($responses as $response) {
            $queue[] = function (RequestInterface $request, array $options) use ($bucket, $response): Response {
                $bucket->requests[] = $request;

                return $response;
            };
        }
        $mock = new MockHandler($queue);
        $http = new Client(['handler' => $mock]);

        $client = new CredentialsClient(
            $http,
            self::API_KEY,
            self::DOMAIN_KEY,
            self::BASE_URL,
            null,
        );

        return [$client, $bucket];
    }

    /**
     * @param \stdClass $bucket from {@see clientWithMockResponses()} with `requests` list
     */
    private function lastRequest(\stdClass $bucket): RequestInterface
    {
        $requests = $bucket->requests;
        $this->assertIsArray($requests);
        $this->assertNotEmpty($requests, 'Expected at least one HTTP request.');

        return $requests[array_key_last($requests)];
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    /**
     * @throws GuzzleException
     */
    public function testCheckCredentialsSendsPostWithKRUQuery(): void
    {
        $bucket = new \stdClass();
        $bucket->requests = [];

        $mock = new MockHandler([
            function (RequestInterface $request, array $options) use ($bucket): Response {
                $bucket->requests[] = $request;

                return new Response(200, [], '{"success":true}');
            },
        ]);
        $http = new Client(['handler' => $mock]);

        $client = new CredentialsClient(
            $http,
            self::API_KEY,
            self::DOMAIN_KEY,
            self::BASE_URL,
            'tracking-key-xyz',
        );

        $result = $client->checkCredentials();

        $this->assertSame(['success' => true], $result);

        $request = $this->lastRequest($bucket);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/check-credentials', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame('tracking-key-xyz', $query['k']);
        $this->assertSame(self::API_KEY, $query['r']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
    }

    public function testCheckCredentialsThrowsWhenTrackingKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new CredentialsClient($client, self::API_KEY, self::DOMAIN_KEY, self::BASE_URL, null);

        $this->expectException(IlluminateValidationException::class);

        $api->checkCredentials();
    }

    /**
     * @throws GuzzleException
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
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
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
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
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
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
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
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
        $this->assertArrayNotHasKey('email', $query);
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
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
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
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
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
        $api = new CredentialsClient($client, self::API_KEY, null, self::BASE_URL);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->getCosts();
    }

    public function testGetCostsThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new CredentialsClient($client, null, self::DOMAIN_KEY, self::BASE_URL);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->getCosts();
    }

    public function testGetDeliveryLogsThrowsWhenEmailInvalid(): void
    {
        [$client] = $this->clientWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $client->getDeliveryLogs(['email' => 'not-an-email']);
    }

    public function testGetEnteredAutomationThrowsWhenDateInvalid(): void
    {
        [$client] = $this->clientWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $client->getEnteredAutomation(['date' => '15-03-2025']);
    }
}
