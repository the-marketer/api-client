<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Psr\Http\Message\RequestInterface;
use TheMarketer\ApiClient\Common\ApiContext;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Gateways\ApiGateway;
use TheMarketer\ApiClient\Gateways\TrackingGateway;

abstract class TestCase extends OrchestraTestCase
{
    protected const MOCK_BASE_URL = 'https://api.example.test';

    protected const MOCK_DOMAIN = 'domain-1';

    protected const MOCK_API_KEY = 'api-secret';

    /** Cheie de tracking nevidă pentru {@see TrackingGateway} în testele care folosesc `context->tracking`. */
    protected const MOCK_TRACKING_KEY = 'track-key-123456789012';

    /**
     * Construiește un {@see ApiContext} cu gateway-uri REST și tracking care folosesc același client Guzzle (ex. mock).
     */
    protected function makeApiContextWithMockClient(Config $config, Client $guzzleClient): ApiContext
    {
        $restGateway = new ApiGateway($config, 0, $guzzleClient);
        $trackingGateway = new TrackingGateway($config, 0, $guzzleClient);

        $context = new ApiContext($config, 0);
        $gatewaysProp = new \ReflectionProperty(ApiContext::class, 'gateways');
        $gatewaysProp->setAccessible(true);
        $gatewaysProp->setValue($context, [
            'rest' => $restGateway,
            'tracking' => $trackingGateway,
        ]);

        return $context;
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $apiClass
     *
     * @return array{0: T, 1: \stdClass}
     *
     * @phpstan-param \stdClass&object{requests: list<RequestInterface>} $bucket
     */
    protected function createApiWithMock(string $apiClass, Response ...$responses): array
    {
        return $this->createApiWithMockUsingConfig(
            new Config(
                self::MOCK_DOMAIN,
                self::MOCK_API_KEY,
                self::MOCK_BASE_URL,
                self::MOCK_BASE_URL,
                self::MOCK_TRACKING_KEY,
            ),
            $apiClass,
            ...$responses,
        );
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $apiClass
     *
     * @return array{0: T, 1: \stdClass}
     *
     * @phpstan-param \stdClass&object{requests: list<RequestInterface>} $bucket
     */
    protected function createApiWithMockUsingConfig(Config $config, string $apiClass, Response ...$responses): array
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
        $context = $this->makeApiContextWithMockClient($config, $client);

        $api = new $apiClass($context);

        return [$api, $bucket];
    }

    /**
     * @param \stdClass $bucket Object with `requests` list from {@see createApiWithMock()} / {@see createApiWithMockUsingConfig()}
     */
    protected function lastRequest(\stdClass $bucket): RequestInterface
    {
        $requests = $bucket->requests;
        $this->assertIsArray($requests);
        $this->assertNotEmpty($requests, 'Expected at least one HTTP request.');

        return $requests[array_key_last($requests)];
    }
}
