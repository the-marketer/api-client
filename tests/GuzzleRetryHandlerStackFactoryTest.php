<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use TheMarketer\ApiClient\Common\GuzzleRetryHandlerStackFactory;

final class GuzzleRetryHandlerStackFactoryTest extends TestCase
{
    public function testRetriesOnceOn503ThenSucceeds(): void
    {
        $mock = new MockHandler([
            new Response(503, [], 'Unavailable'),
            new Response(200, [], '{"ok":true}'),
        ]);
        $stack = GuzzleRetryHandlerStackFactory::createWithZeroDelayForTesting($mock, 1);
        $client = new GuzzleClient(['handler' => $stack]);
        $response = $client->get('http://example.test/r');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"ok":true}', (string) $response->getBody());
    }

    public function testDoesNotRetryOn401(): void
    {
        $mock = new MockHandler([
            new Response(401, [], 'Unauthorized'),
        ]);
        $stack = GuzzleRetryHandlerStackFactory::createWithZeroDelayForTesting($mock, 1);
        $client = new GuzzleClient(['handler' => $stack]);

        $this->expectException(RequestException::class);
        $client->get('http://example.test/r');
    }

    public function testStopsAfterMaxRetries(): void
    {
        $mock = new MockHandler([
            new Response(503, [], 'a'),
            new Response(503, [], 'b'),
        ]);
        $stack = GuzzleRetryHandlerStackFactory::createWithZeroDelayForTesting($mock, 1);
        $client = new GuzzleClient(['handler' => $stack]);

        $this->expectException(RequestException::class);
        $client->get('http://example.test/r');
    }

    public function testZeroRetriesDoesNotIssueSecondRequest(): void
    {
        $mock = new MockHandler([
            new Response(503, [], 'once'),
        ]);
        $stack = GuzzleRetryHandlerStackFactory::createWithZeroDelayForTesting($mock, 0);
        $client = new GuzzleClient(['handler' => $stack]);

        $this->expectException(RequestException::class);
        try {
            $client->get('http://example.test/r');
        } finally {
            $this->assertCount(0, $mock);
        }
    }
}
