<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use NotificationService\Sdk\Internal\LoyaltyApi;
use Psr\Http\Message\RequestInterface;
use TheMarketer\ApiClient\Exception\ValidationException;

final class LoyaltyApiTest extends TestCase
{
    private const BASE_URL = 'https://api.example.test';

    private const DOMAIN_KEY = 'domain-1';

    private const API_KEY = 'api-secret';

    /**
     * @return array{0: LoyaltyApi, 1: \stdClass}
     *
     * @phpstan-param \stdClass&object{requests: list<RequestInterface>} $bucket
     */
    private function apiWithMockResponses(Response ...$responses): array
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
        $client = new Client(['handler' => $mock]);

        $api = new LoyaltyApi(self::DOMAIN_KEY, self::API_KEY, $client, self::BASE_URL);

        return [$api, $bucket];
    }

    /**
     * @param \stdClass $bucket from {@see apiWithMockResponses()} with `requests` list
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
    public function testGetInfoSendsGetWithEmailAndAuthQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"points":100}'),
        );

        $result = $api->getInfo('user@example.com');

        $this->assertSame(['points' => 100], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/loyalty_info', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
        $this->assertSame('user@example.com', $query['email']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testManagePointsSendsPostJsonBodyWithEmailActionPoints(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"success":true}'),
        );

        $result = $api->managePoints('user@example.com', 'increase', 50);

        $this->assertSame(['success' => true], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/manage_loyalty_points', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('user@example.com', $body['email']);
        $this->assertSame('increase', $body['action']);
        $this->assertSame(50, $body['points']);
    }

    public function testManagePointsSendsDecreaseAction(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->managePoints('a@b.com', 'decrease', 1);

        $request = $this->lastRequest($container);
        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('decrease', $body['action']);
        $this->assertSame(1, $body['points']);
    }

    public function testGetInfoThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new LoyaltyApi(null, self::API_KEY, $client, self::BASE_URL);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->getInfo('user@example.com');
    }

    public function testGetInfoThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new LoyaltyApi(self::DOMAIN_KEY, null, $client, self::BASE_URL);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->getInfo('user@example.com');
    }

    public function testManagePointsThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new LoyaltyApi(null, self::API_KEY, $client, self::BASE_URL);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->managePoints('user@example.com', 'increase', 10);
    }

    public function testManagePointsThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new LoyaltyApi(self::DOMAIN_KEY, null, $client, self::BASE_URL);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->managePoints('user@example.com', 'increase', 10);
    }

    public function testGetInfoThrowsWhenEmailInvalid(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->getInfo('not-an-email');
    }

    public function testManagePointsThrowsWhenEmailInvalid(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->managePoints('invalid', 'increase', 10);
    }

    public function testManagePointsThrowsWhenActionInvalid(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->managePoints('user@example.com', 'freeze', 10);
    }

    public function testManagePointsThrowsWhenPointsBelowOne(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->managePoints('user@example.com', 'increase', 0);
    }
}
