<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Builds a Guzzle {@see HandlerStack} like {@see HandlerStack::create()} plus automatic retries
 * for transient failures (connection errors, timeouts, selected HTTP status codes).
 */
final class GuzzleRetryHandlerStackFactory
{
    /**
     * @param  (callable(\Psr\Http\Message\RequestInterface, array): \GuzzleHttp\Promise\PromiseInterface)|null  $handler  Underlying handler (e.g. curl or {@see \GuzzleHttp\Handler\MockHandler}).
     * @param  int  $maxRetryAttempts  Extra attempts after the first request (e.g. 1 = one retry, two HTTP tries max).
     * @param  (callable(int, ?\Psr\Http\Message\ResponseInterface, \Psr\Http\Message\RequestInterface): int)|null  $delay  Milliseconds to wait before each retry; default is capped exponential backoff.
     */
    public static function create(
        ?callable $handler = null,
        int $maxRetryAttempts = 1,
        ?callable $delay = null,
    ): HandlerStack {
        $stack = HandlerStack::create($handler ?? Utils::chooseHandler());
        $delayFn = $delay ?? static function (int $retries, ?ResponseInterface $response, RequestInterface $request): int {
            return self::defaultDelay($retries, $response, $request);
        };
        $stack->push(
            Middleware::retry(
                self::decider($maxRetryAttempts),
                $delayFn,
            ),
            'themarketer_retry',
        );

        return $stack;
    }

    /**
     * @param  int  $maxRetryAttempts  Same as {@see create()}.
     */
    public static function createWithZeroDelayForTesting(
        ?callable $handler = null,
        int $maxRetryAttempts = 1,
    ): HandlerStack {
        return self::create(
            $handler,
            $maxRetryAttempts,
            static fn (int $retries, ?ResponseInterface $response, RequestInterface $request): int => 0,
        );
    }

    /**
     * @return callable(int, \Psr\Http\Message\RequestInterface, ?\Psr\Http\Message\ResponseInterface, mixed): bool
     */
    private static function decider(int $maxRetryAttempts): callable
    {
        return static function (
            int $retries,
            RequestInterface $request,
            ?ResponseInterface $response,
            $exception = null,
        ) use ($maxRetryAttempts): bool {
            if ($retries >= $maxRetryAttempts) {
                return false;
            }

            if ($exception instanceof ConnectException) {
                return true;
            }

            if ($exception instanceof RequestException) {
                if (! $exception->hasResponse()) {
                    return true;
                }

                return self::isRetryableStatus($exception->getResponse()->getStatusCode());
            }

            if ($response !== null) {
                return self::isRetryableStatus($response->getStatusCode());
            }

            return false;
        };
    }

    private static function isRetryableStatus(int $statusCode): bool
    {
        return match ($statusCode) {
            408, 425, 429, 500, 502, 503, 504 => true,
            default => false,
        };
    }

    /**
     * @return callable(int, ?ResponseInterface, RequestInterface): int
     */
    private static function defaultDelay(int $retries, ?ResponseInterface $response, RequestInterface $request): int
    {
        // Milliseconds; first retry ~250ms, then doubles, cap 10s (Guzzle handlers use `delay` as ms).
        $ms = (int) min(250 * (2 ** max(0, $retries - 1)), 10_000);

        return max(0, $ms);
    }
}
