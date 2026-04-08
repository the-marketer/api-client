<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use NotificationService\Sdk\Internal\TransactionalsApi;
use TheMarketer\ApiClient\Common\ApiContext;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\Gateways\ApiGateway;

final class TransactionalsApiTest extends TestCase
{
    /**
     * @return array{0: TransactionalsApi, 1: \stdClass}
     */
    private function apiWithMockResponses(Response ...$responses): array
    {
        return $this->createApiWithMock(TransactionalsApi::class, ...$responses);
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
        $this->assertSame(self::MOCK_API_KEY, $query['k']);
        $this->assertSame(self::MOCK_DOMAIN, $query['u']);

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
        $config = new Config('', self::MOCK_API_KEY, self::MOCK_BASE_URL);
        $api = new TransactionalsApi(new ApiContext(new ApiGateway($config, 0, $client), $config));

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
        $config = new Config(self::MOCK_DOMAIN, '', self::MOCK_BASE_URL);
        $api = new TransactionalsApi(new ApiContext(new ApiGateway($config, 0, $client), $config));

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
        $config = new Config('', self::MOCK_API_KEY, self::MOCK_BASE_URL);
        $api = new TransactionalsApi(new ApiContext(new ApiGateway($config, 0, $client), $config));

        $this->expectException(ValidationException::class);

        $api->sendSms('+40000000000', 'text');
    }

    public function testSendEmailThrowsWhenToIsNotValidEmail(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(ValidationException::class);

        $api->sendEmail([
            'to' => 'not-an-email',
            'subject' => 'x',
            'body' => 'y',
        ]);
    }

    public function testSendSmsThrowsWhenContentEmpty(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(ValidationException::class);

        $api->sendSms('+40700111222', '');
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testSendEmailsBulkSendsPostJsonArrayOfEmailPayloads(): void
    {
        [$api, $container] = $this->apiWithMockResponses(
            new Response(200, [], '{"queued":2}'),
        );

        $result = $api->sendEmailsBulk([
            'emails' => [
                [
                    'to' => 'a@example.com',
                    'subject' => 'One',
                    'body' => '<p>A</p>',
                ],
                [
                    'to' => 'b@example.com',
                    'subject' => 'Two',
                    'body' => 'Plain',
                    'from' => 'shop@example.com',
                ],
            ],
        ]);

        $this->assertSame(['queued' => 2], $result);

        $request = $this->lastRequest($container);
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/transactional/batch-send-email', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertArrayHasKey('emails', $body);
        $this->assertCount(2, $body['emails']);
        $this->assertSame('a@example.com', $body['emails'][0]['to']);
        $this->assertSame('One', $body['emails'][0]['subject']);
        $this->assertSame('b@example.com', $body['emails'][1]['to']);
        $this->assertSame('shop@example.com', $body['emails'][1]['from']);
    }

    public function testSendEmailsBulkThrowsWhenEmailsEmpty(): void
    {
        [$api] = $this->apiWithMockResponses();

        $this->expectException(ValidationException::class);

        $api->sendEmailsBulk(['emails' => []]);
    }
}
