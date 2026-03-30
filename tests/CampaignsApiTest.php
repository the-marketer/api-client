<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use NotificationService\Sdk\Internal\CampaignsApi;
use Psr\Http\Message\RequestInterface;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Exception\ValidationException;

final class CampaignsApiTest extends TestCase
{
    private const BASE_URL = 'https://api.example.test';

    private const DOMAIN_KEY = 'domain-1';

    private const API_KEY = 'api-secret';

    /**
     * @return array{0: CampaignsApi, 1: \stdClass}
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

        $api = new CampaignsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config(self::DOMAIN_KEY, self::API_KEY), self::BASE_URL));

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
     * @return array<string, mixed>
     */
    private function minimalCreateCampaignPayload(): array
    {
        return [
            'type' => 'email',
            'mode' => 'regular',
            'sender' => [
                'sender_name' => 'Shop',
                'sender_email' => 'shop@example.com',
                'reply_to' => 'support@example.com',
            ],
            'audience' => [
                'audience_type' => 'all',
                'smart_sending' => false,
            ],
            'subject' => [
                'name' => 'Spring',
                'subject_line' => 'Hello',
                'preview_text' => 'Preview',
            ],
            'content' => [
                'html' => '<p>Hi</p>',
            ],
            'scheduling' => [
                'send_at' => '2025-06-01 12:00',
                'use_optimal_time' => 0,
                'optimize_for' => 'opening',
            ],
            'tracking' => [
                'utm_campaign' => 'c1',
                'utm_medium' => 'email',
                'utm_source' => 'nl',
            ],
        ];
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testListSendsGetWithAuthQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"items":[]}'),
        );

        $result = $api->list();

        $this->assertSame(['items' => []], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/campaigns/list', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testCreateSendsPostJsonBody(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"id":"new-1"}'),
        );

        $payload = $this->minimalCreateCampaignPayload();
        $result = $api->create($payload);

        $this->assertSame(['id' => 'new-1'], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/campaigns/create', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('email', $body['type']);
        $this->assertSame('regular', $body['mode']);
        $this->assertSame('Shop', $body['sender']['sender_name']);
        $this->assertSame('all', $body['audience']['audience_type']);
        $this->assertSame('<p>Hi</p>', $body['content']['html']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetEmailReportSendsGetWithEncodedCampaignId(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"opens":1}'),
        );

        $result = $api->getEmailReport('foo/bar');

        $this->assertSame(['opens' => 1], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $path = $request->getUri()->getPath();
        $this->assertSame('/campaigns/foo%2Fbar/email/get-report', $path);

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetEmailReportAcceptsIntegerId(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->getEmailReport(99);

        $request = $this->lastRequest($container);
        $this->assertStringContainsString('/campaigns/99/email/get-report', $request->getUri()->getPath());
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetLatestCampaignWithoutLimitSendsGetWithAuthOnly(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '[]'),
        );

        $result = $api->getLatestCampaign();

        $this->assertSame([], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/get-latest-campaign', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
        $this->assertArrayNotHasKey('limit', $query);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetLatestCampaignWithLimitAddsQueryParam(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->getLatestCampaign(5);

        $request = $this->lastRequest($container);
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame('5', $query['limit']);
    }

    public function testListThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new CampaignsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config('', self::API_KEY), self::BASE_URL));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->list();
    }

    public function testListThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new CampaignsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config(self::DOMAIN_KEY, ''), self::BASE_URL));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->list();
    }

    public function testCreateThrowsWhenPayloadInvalid(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->create(['type' => 'invalid-type']);
    }

    public function testGetLatestCampaignThrowsWhenLimitBelowMinimum(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->getLatestCampaign(0);
    }
}
