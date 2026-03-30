<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use NotificationService\Sdk\Internal\SubscribersApi;
use Psr\Http\Message\RequestInterface;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Exception\ValidationException;

final class SubscribersApiTest extends TestCase
{
    private const BASE_URL = 'https://api.example.test';

    private const DOMAIN_KEY = 'domain-1';

    private const API_KEY = 'api-secret';

    /**
     * @return array{0: SubscribersApi, 1: \stdClass}
     *
     * The second element is a bucket with a `requests` property (list of captured requests).
     * An object is used so the list remains shared after the helper returns (PHP arrays are returned by value).
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

        $api = new SubscribersApi(new \TheMarketer\ApiClient\HttpClient($client, new Config(self::DOMAIN_KEY, self::API_KEY), self::BASE_URL));

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
    public function testStatusSubscriberSendsGetWithEmailAndAuthQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"status":"active"}'),
        );

        $result = $api->statusSubscriber('user@example.com');

        $this->assertSame(['status' => 'active'], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/status_subscriber', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
        $this->assertSame('user@example.com', $query['email']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testUnsubscribedEmailsSendsGetWithDateRangeAndAuthQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '["a@x.com"]'),
        );

        $result = $api->unsubscribedEmails('2024-01-01', '2024-01-31');

        $this->assertSame(['a@x.com'], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/unsubscribed_emails', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
        $this->assertSame('2024-01-01', $query['date_from']);
        $this->assertSame('2024-01-31', $query['date_to']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testAddSubscriberSendsPostJsonWithEmailAndOptionalFields(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"ok":true}'),
        );

        $result = $api->addSubscriber([
            'email' => 'user@example.com',
            'addTags' => 'tag1,tag2',
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'phone' => '+40123456789',
            'city' => 'Bucharest',
            'country' => 'RO',
            'birthday' => '1990-05-01',
            'channels' => 'email,sms',
            'attributes' => ['foo' => 'bar'],
        ]);

        $this->assertSame(['ok' => true], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/add_subscriber', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame([
            'email' => 'user@example.com',
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'add_tags' => 'tag1,tag2',
            'phone' => '+40123456789',
            'city' => 'Bucharest',
            'country' => 'RO',
            'birthday' => '1990-05-01',
            'channels' => 'email,sms',
            'attributes' => ['foo' => 'bar'],
        ], $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testAddSubscriberSendsPostJsonWithEmailOnlyWhenOptionalsOmitted(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"ok":true}'),
        );

        $api->addSubscriber(['email' => 'only@example.com']);

        $request = $this->lastRequest($container);
        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(['email' => 'only@example.com'], $body);
    }

    public function testAddSubscriberByPhoneSendsPostJson(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->addSubscriberByPhone('+40700000000', 'Ion', 'Pop');

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/add_subscriber_by_phone', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame([
            'phone' => '+40700000000',
            'firstname' => 'Ion',
            'lastname' => 'Pop',
        ], $body);
    }

    public function testAddSubscriberBulkSendsJsonArrayBody(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"imported":2}'),
        );

        $result = $api->addSubscriberBulk([
            ['email' => 'a@x.com', 'firstname' => 'A'],
            ['email' => 'b@x.com', 'lastname' => 'B'],
        ]);

        $this->assertSame(['imported' => 2], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/add_subscriber_bulk', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame([
            ['email' => 'a@x.com', 'firstname' => 'A'],
            ['email' => 'b@x.com', 'lastname' => 'B'],
        ], $body);
    }

    public function testAddSubscriberBulkThrowsWhenEmpty(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->addSubscriberBulk([]);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testAddSubscriberBulkThrowsWhenRowIsNotArray(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        /** @phpstan-ignore-next-line */
        $api->addSubscriberBulk(['not-an-array']);
    }

    public function testAddSubscriberBulkThrowsWhenAttributesIsNotArray(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->addSubscriberBulk([
            ['email' => 'a@x.com', 'attributes' => 'not-array'],
        ]);
    }

    public function testRemoveSubscriberSendsPostWithEmailInQueryAndOptionalChannels(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"removed":true}'),
        );

        $result = $api->removeSubscriber('user@example.com', 'email');

        $this->assertSame(['removed' => true], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/remove_subscriber', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
        $this->assertSame('user@example.com', $query['email']);
        $this->assertSame('email', $query['channels']);
    }

    public function testAnonymizeEmailSendsPostJsonBody(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"ok":1}'),
        );

        $api->anonymizeEmail('old@example.com');

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/anonymize-email', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(['email' => 'old@example.com'], $body);
    }

    public function testUpdateTagsSendsPostWithQueryParams(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"tags":[]}'),
        );

        $api->updateTags('u@x.com', [1, 'news'], ['old'], 1);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/update-tags', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
        $this->assertSame('u@x.com', $query['email']);
        $this->assertSame('1', (string) $query['overwrite_existing']);
        $this->assertSame(['1', 'news'], $query['add_tags']);
        $this->assertSame(['old'], $query['remove_tags']);
    }

    public function testThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new SubscribersApi(new \TheMarketer\ApiClient\HttpClient($client, new Config('', self::API_KEY), self::BASE_URL));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->statusSubscriber('a@b.com');
    }

    public function testThrowsWhenEmailEmptyForStatusSubscriber(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->statusSubscriber('   ');
    }

    public function testThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new SubscribersApi(new \TheMarketer\ApiClient\HttpClient($client, new Config(self::DOMAIN_KEY, ''), self::BASE_URL));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->statusSubscriber('a@b.com');
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testListUnsubscribedSendsGetWithoutDateParamsWhenBothNull(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '[]'),
        );

        $result = $api->listUnsubscribed();

        $this->assertSame([], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/unsubscribed_emails', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
        $this->assertArrayNotHasKey('date_from', $query);
        $this->assertArrayNotHasKey('date_to', $query);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testListUnsubscribedSendsGetWithDateRange(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '[]'),
        );

        $api->listUnsubscribed('2025-01-01', '2025-01-31');

        $request = $this->lastRequest($container);
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame('2025-01-01', $query['date_from']);
        $this->assertSame('2025-01-31', $query['date_to']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testListSubscribedSendsGetWithOptionalQuery(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"items":[]}'),
        );

        $result = $api->listSubscribed('2024-06-01', null);

        $this->assertSame(['items' => []], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/subscribed_emails', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame('2024-06-01', $query['date_from']);
        $this->assertArrayNotHasKey('date_to', $query);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testSubscribersEvolutionSendsGet(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"series":[]}'),
        );

        $result = $api->subscribersEvolution();

        $this->assertSame(['series' => []], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/subscribers-evolution', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testAddSubscriberSyncSendsPostToAddSubscriberSyncPath(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"synced":true}'),
        );

        $result = $api->addSubscriberSync([
            'email' => 'sync@example.com',
            'firstname' => 'S',
            'lastname' => 'Y',
        ]);

        $this->assertSame(['synced' => true], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/add_subscriber_sync', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame([
            'email' => 'sync@example.com',
            'firstname' => 'S',
            'lastname' => 'Y',
        ], $body);
    }

    public function testDeleteSubscriberSendsPostJsonWithEmailOnly(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"deleted":1}'),
        );

        $result = $api->deleteSubscriber(['email' => 'gone@example.com']);

        $this->assertSame(['deleted' => 1], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/delete_subscriber', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(['email' => 'gone@example.com'], $body);
    }

    public function testDeleteSubscriberSendsPostJsonWithPhoneOnly(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->deleteSubscriber(['phone' => ' +40 700 000 000 ']);

        $request = $this->lastRequest($container);
        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(['phone' => '+40 700 000 000'], $body);
    }

    public function testDeleteSubscriberSendsPostJsonWithEmailAndPhone(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->deleteSubscriber([
            'email' => 'x@y.com',
            'phone' => '+40111222333',
        ]);

        $request = $this->lastRequest($container);
        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame([
            'email' => 'x@y.com',
            'phone' => '+40111222333',
        ], $body);
    }

    public function testDeleteSubscriberThrowsWhenNeitherEmailNorPhoneProvided(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->deleteSubscriber([]);
    }

    public function testAddSubscriberByPhoneSendsPostJsonWithPhoneOnly(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->addSubscriberByPhone('+40700000000');

        $request = $this->lastRequest($container);
        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(['phone' => '+40700000000'], $body);
    }

    public function testRemoveSubscriberOmitsChannelsFromQueryWhenNull(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->removeSubscriber('only@example.com');

        $request = $this->lastRequest($container);
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame('only@example.com', $query['email']);
        $this->assertArrayNotHasKey('channels', $query);
    }

    public function testUpdateTagsSendsPostWithEmailOnlyWhenTagListsEmpty(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->updateTags('plain@example.com');

        $request = $this->lastRequest($container);
        $this->assertStringEndsWith('/update-tags', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame('plain@example.com', $query['email']);
        $this->assertArrayNotHasKey('add_tags', $query);
        $this->assertArrayNotHasKey('remove_tags', $query);
        $this->assertArrayNotHasKey('overwrite_existing', $query);
    }
}
