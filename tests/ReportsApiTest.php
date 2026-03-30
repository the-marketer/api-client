<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use NotificationService\Sdk\Internal\ReportsApi;
use Psr\Http\Message\RequestInterface;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Exception\ValidationException;

final class ReportsApiTest extends TestCase
{
    private const BASE_URL = 'https://api.example.test';

    private const DOMAIN_KEY = 'domain-1';

    private const API_KEY = 'api-secret';

    /**
     * @return array{0: ReportsApi, 1: \stdClass}
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

        $api = new ReportsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config(self::DOMAIN_KEY, self::API_KEY), self::BASE_URL));

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
     * @return array<string, string>
     */
    private function dateRange(): array
    {
        return [
            'start' => '2025-01-01',
            'end' => '2025-01-31',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function emailReportsQuery(): array
    {
        return array_merge($this->dateRange(), ['type' => 'sent']);
    }

    /**
     * @return array<string, string>
     */
    private function smsPushReportsQuery(): array
    {
        return array_merge($this->dateRange(), ['type' => 'sent']);
    }

    /**
     * @return array<string, string>
     */
    private function formsReportsQuery(): array
    {
        return array_merge($this->dateRange(), ['type' => 'total-impressions']);
    }

    /**
     * @return array<string, string>
     */
    private function audienceReportsQuery(): array
    {
        return array_merge($this->dateRange(), ['type' => 'total-subscribed-emails']);
    }

    /**
     * @param array<string, string> $expectedSubset
     */
    private function assertReportsGetRequest(RequestInterface $request, string $pathSuffix, array $expectedSubset): void
    {
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/reports/' . $pathSuffix, $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);

        foreach ($expectedSubset as $key => $value) {
            $this->assertSame($value, $query[$key] ?? null, 'Query key: ' . $key);
        }
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetEmailCampaignsSendsGetWithQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"rows":[]}'),
        );

        $q = $this->emailReportsQuery();
        $result = $api->getEmailCampaigns($q);

        $this->assertSame(['rows' => []], $result);

        $this->assertReportsGetRequest(
            $this->lastRequest($container),
            'get-email-campaigns',
            [
                'type' => 'sent',
                'start' => '2025-01-01',
                'end' => '2025-01-31',
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetEmailAutomationSendsGetWithQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->getEmailAutomation($this->emailReportsQuery());

        $this->assertReportsGetRequest(
            $this->lastRequest($container),
            'get-email-automation',
            [
                'type' => 'sent',
                'start' => '2025-01-01',
                'end' => '2025-01-31',
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetPushCampaignsSendsGetWithQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->getPushCampaigns($this->smsPushReportsQuery());

        $this->assertReportsGetRequest(
            $this->lastRequest($container),
            'get-push-campaigns',
            [
                'type' => 'sent',
                'start' => '2025-01-01',
                'end' => '2025-01-31',
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetPushAutomationSendsGetWithQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->getPushAutomation($this->smsPushReportsQuery());

        $this->assertReportsGetRequest(
            $this->lastRequest($container),
            'get-push-automation',
            [
                'type' => 'sent',
                'start' => '2025-01-01',
                'end' => '2025-01-31',
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetSmsCampaignsSendsGetWithQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->getSmsCampaigns($this->smsPushReportsQuery());

        $this->assertReportsGetRequest(
            $this->lastRequest($container),
            'get-sms-campaigns',
            [
                'type' => 'sent',
                'start' => '2025-01-01',
                'end' => '2025-01-31',
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetSmsAutomationSendsGetWithQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->getSmsAutomation($this->smsPushReportsQuery());

        $this->assertReportsGetRequest(
            $this->lastRequest($container),
            'get-sms-automation',
            [
                'type' => 'sent',
                'start' => '2025-01-01',
                'end' => '2025-01-31',
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetFormsPopupsSendsGetWithQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->getFormsPopups($this->formsReportsQuery());

        $this->assertReportsGetRequest(
            $this->lastRequest($container),
            'get-forms-popups',
            [
                'type' => 'total-impressions',
                'start' => '2025-01-01',
                'end' => '2025-01-31',
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetFormsEmbeddedSendsGetWithQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->getFormsEmbedded($this->formsReportsQuery());

        $this->assertReportsGetRequest(
            $this->lastRequest($container),
            'get-forms-embedded',
            [
                'type' => 'total-impressions',
                'start' => '2025-01-01',
                'end' => '2025-01-31',
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetAudienceSendsGetWithQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->getAudience($this->audienceReportsQuery());

        $this->assertReportsGetRequest(
            $this->lastRequest($container),
            'get-audience',
            [
                'type' => 'total-subscribed-emails',
                'start' => '2025-01-01',
                'end' => '2025-01-31',
            ],
        );
    }

    public function testGetEmailCampaignsThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new ReportsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config('', self::API_KEY), self::BASE_URL));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->getEmailCampaigns($this->emailReportsQuery());
    }

    public function testGetEmailCampaignsThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new ReportsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config(self::DOMAIN_KEY, ''), self::BASE_URL));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->getEmailCampaigns($this->emailReportsQuery());
    }

    public function testGetEmailCampaignsThrowsWhenStartMissing(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->getEmailCampaigns([
            'type' => 'sent',
            'end' => '2025-01-31',
        ]);
    }
}
