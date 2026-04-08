<?php

declare(strict_types=1);

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\Gateways\ApiGateway;
use TheMarketer\ApiClient\Gateways\TrackingGateway;

final class GatewaysAuthTest extends TestCase
{
    public function testApiGatewayGetThrowsWhenCustomerIdMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $config = new Config('', self::MOCK_API_KEY, self::MOCK_BASE_URL);
        $gw = new ApiGateway($config, 0, $client);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $gw->get('/x');
    }

    public function testApiGatewayGetThrowsWhenRestKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $config = new Config(self::MOCK_DOMAIN, '', self::MOCK_BASE_URL);
        $gw = new ApiGateway($config, 0, $client);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $gw->get('/x');
    }

    public function testTrackingGatewayGetThrowsWhenTrackingKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $config = new Config(self::MOCK_DOMAIN, self::MOCK_API_KEY, self::MOCK_BASE_URL, self::MOCK_BASE_URL, '');
        $gw = new TrackingGateway($config, 0, $client);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Tracking key not provided.');

        $gw->get('/t/r');
    }
}
