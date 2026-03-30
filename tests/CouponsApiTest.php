<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use NotificationService\Sdk\Internal\CouponsApi;
use Psr\Http\Message\RequestInterface;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Exception\ValidationException;

final class CouponsApiTest extends TestCase
{
    private const BASE_URL = 'https://api.example.test';

    private const DOMAIN_KEY = 'domain-1';

    private const API_KEY = 'api-secret';

    /**
     * @return array{0: CouponsApi, 1: \stdClass}
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

        $api = new CouponsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config(self::DOMAIN_KEY, self::API_KEY), self::BASE_URL));

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
    private function validSavePayload(): array
    {
        return [
            'code' => 'WELCOME10',
            'type' => '1',
            'value' => '10',
            'expiration_date' => '2026-12-31',
        ];
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testGetAvailableCouponsSendsGetWithEmailAndAuthQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"coupons":[]}'),
        );

        $result = $api->getAvailableCoupons('user@example.com');

        $this->assertSame(['coupons' => []], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/get_available_coupons', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
        $this->assertSame('user@example.com', $query['email']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testSaveSendsPostJsonBodyWithCouponFields(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"saved":true}'),
        );

        $payload = $this->validSavePayload();
        $result = $api->save($payload);

        $this->assertSame(['saved' => true], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/save_coupon', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($payload['code'], $body['code']);
        $this->assertSame($payload['type'], $body['type']);
        $this->assertSame($payload['value'], $body['value']);
        $this->assertSame($payload['expiration_date'], $body['expiration_date']);
    }

    public function testGetAvailableCouponsThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new CouponsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config('', self::API_KEY), self::BASE_URL));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->getAvailableCoupons('user@example.com');
    }

    public function testGetAvailableCouponsThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new CouponsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config(self::DOMAIN_KEY, ''), self::BASE_URL));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->getAvailableCoupons('user@example.com');
    }

    public function testSaveThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new CouponsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config('', self::API_KEY), self::BASE_URL));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->save($this->validSavePayload());
    }

    public function testSaveThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new CouponsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config(self::DOMAIN_KEY, ''), self::BASE_URL));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->save($this->validSavePayload());
    }

    public function testGetAvailableCouponsThrowsWhenEmailInvalid(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->getAvailableCoupons('not-an-email');
    }

    public function testSaveThrowsWhenCodeEmpty(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $payload = $this->validSavePayload();
        $payload['code'] = '';
        $api->save($payload);
    }

    public function testSaveThrowsWhenExpirationDateMissing(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $payload = $this->validSavePayload();
        unset($payload['expiration_date']);
        $api->save($payload);
    }
}
