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

abstract class TestCase extends OrchestraTestCase
{
    protected const MOCK_BASE_URL = 'https://api.example.test';

    protected const MOCK_DOMAIN = 'domain-1';

    protected const MOCK_API_KEY = 'api-secret';

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
            new Config(self::MOCK_DOMAIN, self::MOCK_API_KEY, self::MOCK_BASE_URL),
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
        $gateway = new ApiGateway($config, 0, $client);
        $api = new $apiClass(new ApiContext($gateway, $config));

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
