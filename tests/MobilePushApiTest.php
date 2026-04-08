<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use NotificationService\Sdk\Internal\MobilePushApi;
use TheMarketer\ApiClient\Common\ApiContext;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\Gateways\ApiGateway;

final class MobilePushApiTest extends TestCase
{
    /**
     * @return array{0: MobilePushApi, 1: \stdClass}
     */
    private function apiWithMockResponses(Response ...$responses): array
    {
        return $this->createApiWithMock(MobilePushApi::class, ...$responses);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testSetTokenSendsPostJsonBodyWithIos(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"ok":true}'),
        );

        $result = $api->setToken('user@example.com', 'device-token-xyz', 'ios');

        $this->assertSame(['ok' => true], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/app-push-notifications/token/set', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::MOCK_API_KEY, $query['k']);
        $this->assertSame(self::MOCK_DOMAIN, $query['u']);

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('user@example.com', $body['email']);
        $this->assertSame('device-token-xyz', $body['token']);
        $this->assertSame('ios', $body['type']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testSetTokenSendsPostJsonBodyWithAndroid(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->setToken('a@b.com', 't', 'android');

        $request = $this->lastRequest($container);
        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('android', $body['type']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testRemoveTokenSendsPostJsonBody(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"removed":true}'),
        );

        $result = $api->removeToken('user@example.com', 'ios');

        $this->assertSame(['removed' => true], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/app-push-notifications/token/remove', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::MOCK_API_KEY, $query['k']);
        $this->assertSame(self::MOCK_DOMAIN, $query['u']);

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('user@example.com', $body['email']);
        $this->assertSame('ios', $body['type']);
    }

    public function testSetTokenThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $config = new Config('', self::MOCK_API_KEY, self::MOCK_BASE_URL);
        $api = new MobilePushApi(new ApiContext(new ApiGateway($config, 0, $client), $config));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->setToken('user@example.com', 't', 'ios');
    }

    public function testSetTokenThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $config = new Config(self::MOCK_DOMAIN, '', self::MOCK_BASE_URL);
        $api = new MobilePushApi(new ApiContext(new ApiGateway($config, 0, $client), $config));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->setToken('user@example.com', 't', 'ios');
    }

    public function testSetTokenThrowsWhenEmailInvalid(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(ValidationException::class);

        $api->setToken('not-an-email', 't', 'ios');
    }

    public function testRemoveTokenThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $config = new Config('', self::MOCK_API_KEY, self::MOCK_BASE_URL);
        $api = new MobilePushApi(new ApiContext(new ApiGateway($config, 0, $client), $config));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->removeToken('user@example.com', 'ios');
    }

    public function testRemoveTokenThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $config = new Config(self::MOCK_DOMAIN, '', self::MOCK_BASE_URL);
        $api = new MobilePushApi(new ApiContext(new ApiGateway($config, 0, $client), $config));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->removeToken('user@example.com', 'ios');
    }

    public function testRemoveTokenThrowsWhenEmailInvalid(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(ValidationException::class);

        $api->removeToken('invalid', 'ios');
    }
}
