<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use NotificationService\Sdk\Internal\TransactionalsApi;
use Psr\Http\Message\RequestInterface;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Exception\ValidationException;

final class TransactionalsApiTest extends TestCase
{
    private const BASE_URL = 'https://api.example.test';

    private const DOMAIN_KEY = 'domain-1';

    private const API_KEY = 'api-secret';

    /**
     * @return array{0: TransactionalsApi, 1: \stdClass}
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

        $api = new TransactionalsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config(self::DOMAIN_KEY, self::API_KEY), self::BASE_URL));

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
    public function testSendEmailSendsPostJsonToTransactionalSendEmail(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"sent":true}'),
        );

        $result = $api->sendEmail([
            'to' => 'user@example.com',
            'subject' => 'Hello',
            'body' => '<p>Hi</p>',
        ]);

        $this->assertSame(['sent' => true], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/transactional/send-email', $request->getUri()->getPath());

        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame(self::API_KEY, $query['k']);
        $this->assertSame(self::DOMAIN_KEY, $query['u']);

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('user@example.com', $body['to']);
        $this->assertSame('Hello', $body['subject']);
        $this->assertSame('<p>Hi</p>', $body['body']);
        $this->assertArrayNotHasKey('from', $body);
        $this->assertArrayNotHasKey('reply_to', $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testSendEmailIncludesOptionalFieldsWhenPresent(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{}'),
        );

        $api->sendEmail([
            'to' => 'user@example.com',
            'subject' => 'S',
            'body' => 'B',
            'from' => ' sender@example.com ',
            'reply_to' => 'reply@example.com',
            'attachments' => [['name' => 'a.txt', 'content' => 'eA==']],
        ]);

        $body = json_decode((string) $this->lastRequest($container)->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('sender@example.com', $body['from']);
        $this->assertSame('reply@example.com', $body['reply_to']);
        $this->assertSame([['name' => 'a.txt', 'content' => 'eA==']], $body['attachments']);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testSendSmsSendsPostJsonWithToAndContent(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"ok":1}'),
        );

        $result = $api->sendSms('+40700111222', 'Your code is 1234');

        $this->assertSame(['ok' => 1], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/transactional/send-sms', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('+40700111222', $body['to']);
        $this->assertSame('Your code is 1234', $body['content']);
    }

    public function testSendEmailThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new TransactionalsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config('', self::API_KEY), self::BASE_URL));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Customer ID not provided.');

        $api->sendEmail([
            'to' => 'a@example.com',
            'subject' => 'x',
            'body' => 'y',
        ]);
    }

    public function testSendEmailThrowsWhenApiKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new TransactionalsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config(self::DOMAIN_KEY, ''), self::BASE_URL));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Rest key not provided.');

        $api->sendEmail([
            'to' => 'a@example.com',
            'subject' => 'x',
            'body' => 'y',
        ]);
    }

    public function testSendSmsThrowsWhenDomainKeyMissing(): void
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200)]))]);
        $api = new TransactionalsApi(new \TheMarketer\ApiClient\HttpClient($client, new Config('', self::API_KEY), self::BASE_URL));

        $this->expectException(ValidationException::class);

        $api->sendSms('+40000000000', 'text');
    }

    public function testSendEmailThrowsWhenToIsNotValidEmail(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->sendEmail([
            'to' => 'not-an-email',
            'subject' => 'x',
            'body' => 'y',
        ]);
    }

    public function testSendSmsThrowsWhenContentEmpty(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(IlluminateValidationException::class);

        $api->sendSms('+40700111222', '');
    }
}
