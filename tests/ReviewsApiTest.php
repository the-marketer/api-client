<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use NotificationService\Sdk\Internal\ReviewsApi;
use Psr\Http\Message\RequestInterface;
use TheMarketer\ApiClient\Exception\ValidationException;

final class ReviewsApiTest extends TestCase
{
    private const BASE_URL = 'https://api.example.test';

    private const DOMAIN_KEY = 'domain-1';

    private const API_KEY = 'api-secret';

    /**
     * @return array{0: ReviewsApi, 1: \stdClass}
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

        $api = new ReviewsApi(self::DOMAIN_KEY, self::API_KEY, $client, self::BASE_URL);

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
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetSendsGetToProductReviewsWithAuthQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"reviews":[]}'),
        );

        $result = $api->get();

        $this->assertSame(['reviews' => []], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/product_reviews', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetMergesPaginationAndTypeQueryParams(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->get([
            'page' => 2,
            'perPage' => 20,
            't' => 5,
        ]);

        $request = $this->lastRequest($container);
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame('2', $query['page']);
        $this->assertSame('20', $query['perPage']);
        $this->assertSame('5', $query['t']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testCreateSendsPostJsonBodyToAddReview(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"ok":true}'),
        );

        $result = $api->create([
            'order_id' => 'ord-1',
            'review_date' => '2025-03-26',
        ]);

        $this->assertSame(['ok' => true], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/add_review', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('ord-1', $body['order_id']);
        $this->assertSame('2025-03-26', $body['review_date']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testCreateSendsNestedProductArrays(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->create([
            'order_id' => 'o1',
            'review_date' => '2025-01-01',
            'order_rating' => '5',
            'order_review' => 'Great',
            'product_rating' => [0 => ['3333' => '5']],
            'product_review' => [0 => ['3333' => 'Nice']],
            'media_files' => [0 => ['3333' => 'https://cdn.example/img.jpg']],
        ]);

        $body = json_decode((string) $this->lastRequest($container)->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('5', $body['order_rating']);
        $this->assertSame('Great', $body['order_review']);
        $this->assertSame([0 => ['3333' => '5']], $body['product_rating']);
        $this->assertSame([0 => ['3333' => 'Nice']], $body['product_review']);
        $this->assertSame([0 => ['3333' => 'https://cdn.example/img.jpg']], $body['media_files']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testMerchantAddReviewSendsPostJsonWithNormalizedEmailAndProductId(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"id":1}'),
        );

        $result = $api->merchantAddReview([
            'email' => 'User@Example.COM',
            'product_id' => '42',
            'rating' => 5,
            'content' => 'Good',
        ]);

        $this->assertSame(['id' => 1], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/merchant_add_review', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('user@example.com', $body['email'], 'email is normalized to lowercase in the request body');
        $this->assertSame('42', $body['product_id']);
        $this->assertSame(5, $body['rating']);
        $this->assertSame('Good', $body['content']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testMerchantProSettingSendsPostJsonOmittingEmptyStrings(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->merchantProSetting([
            'product_feed_url' => ' https://feeds.example/p.xml ',
            'inventory_feed_url' => '',
            'api_key' => 'secret',
        ]);

        $body = json_decode((string) $this->lastRequest($container)->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('https://feeds.example/p.xml', $body['product_feed_url']);
        $this->assertSame('secret', $body['api_key']);
        $this->assertArrayNotHasKey('inventory_feed_url', $body);
    }

    public function testGetThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new ReviewsApi(null, self::API_KEY, $client, self::BASE_URL);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->get();
    }

    public function testGetThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new ReviewsApi(self::DOMAIN_KEY, null, $client, self::BASE_URL);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->get();
    }

    public function testCreateThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new ReviewsApi(null, self::API_KEY, $client, self::BASE_URL);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->create(['order_id' => 'x', 'review_date' => '2025-01-01']);
    }

    public function testCreateThrowsWhenOrderIdMissing(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->create(['review_date' => '2025-01-01']);
    }

    public function testMerchantAddReviewThrowsWhenEmailInvalid(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->merchantAddReview([
            'email' => 'not-email',
            'product_id' => 1,
        ]);
    }
}
