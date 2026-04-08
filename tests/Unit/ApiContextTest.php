<?php

declare(strict_types=1);

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;
use TheMarketer\ApiClient\Common\ApiContext;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Gateways\ApiGateway;
use TheMarketer\ApiClient\Gateways\TrackingGateway;

final class ApiContextTest extends TestCase
{
    public function testGetRestReturnsSameGatewayInstanceOnRepeatedAccess(): void
    {
        $config = new Config(self::MOCK_DOMAIN, self::MOCK_API_KEY, self::MOCK_BASE_URL);
        $ctx = new ApiContext($config, 1);

        $this->assertSame($ctx->rest, $ctx->rest);
    }

    public function testGetTrackingReturnsSameGatewayInstanceOnRepeatedAccess(): void
    {
        $config = new Config(
            self::MOCK_DOMAIN,
            self::MOCK_API_KEY,
            self::MOCK_BASE_URL,
            self::MOCK_BASE_URL,
            self::MOCK_TRACKING_KEY,
        );
        $ctx = new ApiContext($config, 1);

        $this->assertSame($ctx->tracking, $ctx->tracking);
    }

    public function testUnknownGatewayNameThrowsInvalidArgumentException(): void
    {
        $config = new Config(self::MOCK_DOMAIN, self::MOCK_API_KEY, self::MOCK_BASE_URL);
        $ctx = new ApiContext($config, 1);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown gateway:');

        $ctx->not_a_gateway;
    }

    public function testTrackingMethodReturnsTrackingGateway(): void
    {
        $config = new Config(
            self::MOCK_DOMAIN,
            self::MOCK_API_KEY,
            self::MOCK_BASE_URL,
            self::MOCK_BASE_URL,
            self::MOCK_TRACKING_KEY,
        );
        $ctx = new ApiContext($config, 1);

        $this->assertInstanceOf(TrackingGateway::class, $ctx->tracking());
    }

    public function testPreInjectedRestGatewayIsReturnedByPropertyAccess(): void
    {
        $config = new Config(self::MOCK_DOMAIN, self::MOCK_API_KEY, self::MOCK_BASE_URL);
        $guzzle = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200, [], '{}')]))]);
        $rest = new ApiGateway($config, 0, $guzzle);

        $ctx = new ApiContext($config, 0);
        $prop = new \ReflectionProperty(ApiContext::class, 'gateways');
        $prop->setAccessible(true);
        $prop->setValue($ctx, ['rest' => $rest]);

        $this->assertSame($rest, $ctx->rest);
    }
}
