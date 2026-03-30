<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use NotificationService\Sdk\Internal\EventsApi;
use Psr\Http\Message\RequestInterface;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Exception\ValidationException;

final class EventsApiTest extends TestCase
{
    private const BASE_URL = 'https://api.example.test';

    private const DOMAIN_KEY = 'domain-1';

    private const API_KEY = 'api-secret';

    /**
     * @return array{0: EventsApi, 1: \stdClass}
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

        $api = new EventsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config(self::DOMAIN_KEY, self::API_KEY), self::BASE_URL));

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
    private function validSendCustomEventPayload(): array
    {
        return [
            'email' => 'user@example.com',
            'event' => 'product_viewed',
        ];
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testSendCustomEventSendsPostJsonBodyWithEmailAndEvent(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"ok":true}'),
        );

        $payload = $this->validSendCustomEventPayload();
        $result = $api->sendCustomEvent($payload);

        $this->assertSame(['ok' => true], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/custom_events', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($payload['email'], $body['email']);
        $this->assertSame($payload['event'], $body['event']);
    }

    public function testSendCustomEventThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new EventsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config('', self::API_KEY), self::BASE_URL));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->sendCustomEvent($this->validSendCustomEventPayload());
    }

    public function testSendCustomEventThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new EventsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config(self::DOMAIN_KEY, ''), self::BASE_URL));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->sendCustomEvent($this->validSendCustomEventPayload());
    }

    public function testSendCustomEventThrowsWhenEmailInvalid(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $payload = $this->validSendCustomEventPayload();
        $payload['email'] = 'not-an-email';
        $api->sendCustomEvent($payload);
    }

    public function testSendCustomEventThrowsWhenEventMissing(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $payload = $this->validSendCustomEventPayload();
        unset($payload['event']);
        $api->sendCustomEvent($payload);
    }
}
