<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use NotificationService\Sdk\Internal\OrdersApi;
use Psr\Http\Message\RequestInterface;
use TheMarketer\ApiClient\Exception\ValidationException;

final class OrdersApiTest extends TestCase
{
    private const BASE_URL = 'https://api.example.test';

    private const DOMAIN_KEY = 'domain-1';

    private const API_KEY = 'api-secret';

    /**
     * @return array{0: OrdersApi, 1: \stdClass}
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

        $api = new OrdersApi(self::DOMAIN_KEY, self::API_KEY, $client, self::BASE_URL);

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
    private function minimalValidSaveOrderProducts(): array
    {
        return [
            [
                'product_id' => 100,
                'price' => 19.99,
                'quantity' => 1,
                'variation_sku' => 'VAR-1',
            ],
        ];
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testUpdateOrderStatusSendsGetWithQueryParams(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"ok":true}'),
        );

        $result = $api->updateOrderStatus('ORD-42', 'shipped');

        $this->assertSame(['ok' => true], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/update_order_status', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
        $this->assertSame('ORD-42', $query['order_number']);
        $this->assertSame('shipped', $query['order_status']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testSaveOrderSendsPostJsonBody(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"saved":1}'),
        );

        $products = $this->minimalValidSaveOrderProducts();

        $result = $api->saveOrder([
            'number' => 1001,
            'email_address' => 'buyer@example.com',
            'phone' => '+40123456789',
            'firstname' => 'Ana',
            'lastname' => 'Ion',
            'city' => 'Bucharest',
            'county' => 'RO',
            'address' => 'Str. X 1',
            'discount_value' => '0',
            'discount_code' => 'NONE',
            'shipping' => '5',
            'tax' => '2',
            'total_value' => '100',
            'products' => $products,
        ]);

        $this->assertSame(['saved' => 1], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/save_order', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(1001, $body['number']);
        $this->assertSame('buyer@example.com', $body['email_address']);
        $this->assertSame($products, $body['products']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testSaveOrderRetailSendsPostToRetailPath(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $products = $this->minimalValidSaveOrderProducts();

        $api->saveOrderRetail([
            'number' => 9,
            'email_address' => 'r@example.com',
            'phone' => '+40',
            'firstname' => 'A',
            'lastname' => 'B',
            'city' => 'C',
            'county' => 'D',
            'address' => 'E',
            'discount_value' => 0,
            'discount_code' => 'NONE',
            'shipping' => '1',
            'tax' => '0',
            'total_value' => '10',
            'products' => $products,
        ]);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/save_order_retail', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(9, $body['number']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testUpdateFeedUrlSendsPostJsonUrlOnly(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"updated":true}'),
        );

        $result = $api->updateFeedUrl('https://example.com/feed.xml');

        $this->assertSame(['updated' => true], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/update_feed_url', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(['url' => 'https://example.com/feed.xml'], $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testUpdateFeedUrlSendsPostJsonWithType(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->updateFeedUrl('https://cdn.example.com/p.xml', 'product');

        $body = json_decode((string) $this->lastRequest($container)->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame([
            'url' => 'https://cdn.example.com/p.xml',
            'type' => 'product',
        ], $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testUpdateOrderFeedUrlSendsPostToOrderFeedPath(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->updateOrderFeedUrl('https://orders.example.com/o.xml', 'category');

        $request = $this->lastRequest($container);
        $this->assertStringEndsWith('/update_order_feed_url', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame([
            'url' => 'https://orders.example.com/o.xml',
            'type' => 'category',
        ], $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetEcommerceStatsSendsGet(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"orders":0}'),
        );

        $result = $api->getEcommerceStats();

        $this->assertSame(['orders' => 0], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/get-ecommerce-stats', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
    }

    public function testThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new OrdersApi(null, self::API_KEY, $client, self::BASE_URL);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->getEcommerceStats();
    }

    public function testThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new OrdersApi(self::DOMAIN_KEY, null, $client, self::BASE_URL);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->getEcommerceStats();
    }

    public function testUpdateFeedUrlThrowsWhenUrlInvalid(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->updateFeedUrl('not-a-valid-url');
    }
}
