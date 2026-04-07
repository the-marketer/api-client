<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use NotificationService\Sdk\Internal\EventsApi;
use TheMarketer\ApiClient\ApiGateway;
use TheMarketer\ApiClient\Common\ApiContext;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Exception\ValidationException;

final class EventsApiTest extends TestCase
{
    /**
     * @return array{0: EventsApi, 1: \stdClass}
     */
    private function apiWithMockResponses(Response ...$responses): array
    {
        return $this->createApiWithMock(EventsApi::class, ...$responses);
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
        $this->assertSame(self::MOCK_API_KEY, $query['k']);
        $this->assertSame(self::MOCK_DOMAIN, $query['u']);

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($payload['email'], $body['email']);
        $this->assertSame($payload['event'], $body['event']);
    }

    public function testSendCustomEventThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $config = new Config('', self::MOCK_API_KEY, self::MOCK_BASE_URL);
        $api = new EventsApi(new ApiContext(new ApiGateway($config, 0, $client), $config));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->sendCustomEvent($this->validSendCustomEventPayload());
    }

    public function testSendCustomEventThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $config = new Config(self::MOCK_DOMAIN, '', self::MOCK_BASE_URL);
        $api = new EventsApi(new ApiContext(new ApiGateway($config, 0, $client), $config));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->sendCustomEvent($this->validSendCustomEventPayload());
    }

    public function testSendCustomEventThrowsWhenEmailInvalid(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(ValidationException::class);

        $payload = $this->validSendCustomEventPayload();
        $payload['email'] = 'not-an-email';
        $api->sendCustomEvent($payload);
    }

    public function testSendCustomEventThrowsWhenEventMissing(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(\ArgumentCountError::class);

        $payload = $this->validSendCustomEventPayload();
        unset($payload['event']);
        $api->sendCustomEvent($payload);
    }
}
