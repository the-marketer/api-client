<?php

namespace TheMarketer\ApiClient;

require_once __DIR__.'/bootstrap_laravel_data.php';

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use JsonException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;

class HttpClient
{
    public const DEFAULT_BASE_URL = 'https://t.themarketer.com/api/v1';

    protected readonly string $baseUrl;

    public function __construct(
        protected readonly ClientInterface $httpClient,
        protected readonly ?string $domainKey,
        protected readonly ?string $domainApiKey,
        ?string $baseUrl = null,
    ) {
        $this->baseUrl = self::normalizeBaseUrl($baseUrl);
        bootstrap_laravel_data_if_needed();
    }

    private static function normalizeBaseUrl(?string $baseUrl): string
    {
        if ($baseUrl !== null && trim($baseUrl) !== '') {
            return rtrim(trim($baseUrl), '/');
        }

        return self::DEFAULT_BASE_URL;
    }

    /**
     * Sends the request and maps HTTP error responses to typed exceptions using the JSON
     * `message` field when present (reusable for subscribers, orders, etc.).
     *
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws GuzzleException
     */
    protected function sendJson(RequestInterface $request): ResponseInterface
    {
        $response = $this->httpClient->send($request);
        if ($response->getStatusCode() >= 400) {
            $this->throwForErrorResponse($response);
        }
        return $response;
    }

    /**
     * Extracts a human-readable message from API JSON `{ "message": ... }` or falls back to the raw body.
     */
    protected function decodeApiErrorMessage(ResponseInterface $response): string
    {
        $body = (string) $response->getBody();
        if ($body === '') {
            return 'Request failed';
        }

        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            if (is_array($decoded) && array_key_exists('message', $decoded)) {
                $m = $decoded['message'];
                if (is_string($m)) {
                    return $m;
                }
                if (is_bool($m)) {
                    return $m ? 'true' : 'false';
                }
                if (is_scalar($m)) {
                    return (string) $m;
                }

                $encoded = json_encode($m);
                return $encoded !== false ? $encoded : 'Invalid response';
            }
        } catch (JsonException) {
            // use truncated body below
        }

        return mb_strlen($body) > 500 ? mb_substr($body, 0, 500).'…' : $body;
    }

    /**
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     */
    protected function throwForErrorResponse(ResponseInterface $response): void
    {
        $status = $response->getStatusCode();
        $message = $this->decodeApiErrorMessage($response);

        match ($status) {
            401 => throw new UnauthorizedException($message),
            404 => throw new CustomerNotFoundException($message),
            405 => throw new MethodNotAllowedException($message),
            default => throw new ApiException($message, $status),
        };
    }

    protected function getRequest(string $path, array $query = []): Request
    {
        $this->assertDomainAuthPresent();

        return $this->request('GET', $path, $this->headers(), null, array_merge($this->domainAuthQuery(), $query));
    }

    /**
     * @param  array<string, scalar>  $query
     * @param  array<string, string>  $extraHeaders  Merged with JSON headers; later keys override earlier ones.
     * @param  bool  $mergeDomainAuthQuery  When false, only {@see $query} is sent (no {@see domainAuthQuery()} `k`/`u`).
     */
    protected function postRequest(
        string $path,
        array $body,
        array $query = [],
        array $extraHeaders = [],
        bool $mergeDomainAuthQuery = true,
    ): Request {
        $this->assertDomainAuthPresent();

        $finalQuery = $mergeDomainAuthQuery ? array_merge($this->domainAuthQuery(), $query) : $query;

        return $this->jsonRequest('POST', $path, $body, $extraHeaders, $finalQuery);
    }

    /**
     * @throws ValidationException
     */
    private function assertDomainAuthPresent(): void
    {
        if ($this->domainKey === null || $this->domainKey === '') {
            throw new ValidationException('Customer ID not provided.', 400);
        }

        if ($this->domainApiKey === null || $this->domainApiKey === '') {
            throw new ValidationException('Rest key not provided.', 400);
        }
    }

    /**
     * @throws ValidationException
     */
    protected function assertNonEmptyString(string $value, string $fieldName): void
    {
        if (trim($value) === '') {
            throw new ValidationException(sprintf('Field "%s" is required.', $fieldName), 400);
        }
    }

    /** @return array<string, scalar> */
    protected function domainAuthQuery(): array
    {
        if ($this->domainKey === null || $this->domainKey === '' || $this->domainApiKey === null || $this->domainApiKey === '') {
            return [];
        }

        return [
            'k' => $this->domainApiKey,
            'u' => $this->domainKey,
        ];
    }

    /** @return array<string, string> */
    private function headers(): array
    {
        $headers = $this->jsonHeaders();

        return $headers;
    }

    protected function jsonHeaders(): array
    {
        return [
            'Content-Type' => "application/json",
            'Accept' => "application/json",
        ];
    }

    protected function jsonRequest(
        string $method,
        string $path,
        array $body,
        array $headers = [],
        array $query = [],
    ): Request {
        return $this->request(
            $method,
            $path,
            array_merge($this->jsonHeaders(), $headers),
            $body,
            $query,
        );
    }

    protected function request(
        string $method,
        string $path,
        array $headers = [],
        array|string|null $body = null,
        array $query = [],
    ): Request {
        return new Request($method, $this->url($path, $query), $headers, $this->prepareBody($headers, $body));
    }

    /**
     * @throws JsonException
     */
    protected function decodeJson(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();
        if ($body === '') {
            return [];
        }

        $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }

    protected function url(string $path, array $query = []): string
    {
        if ($query === []) {
            return rtrim($this->baseUrl, '/').$path;
        }

        return rtrim($this->baseUrl, '/').$path.'?'.http_build_query($query);
    }

    /**
     * @param  array<string, string>  $headers
     * @param  array<string, mixed>|string|null  $body
     */
    private function prepareBody(array $headers, array|string|null $body): ?string
    {
        if ($body === null || is_string($body)) {
            return $body;
        }

        $contentType = strtolower($headers['Content-Type'] ?? $headers['content-type'] ?? '');

        return match (true) {
            str_contains($contentType, 'application/x-www-form-urlencoded') => http_build_query($body, '', '&', PHP_QUERY_RFC1738),
            default => json_encode($body),
        };
    }
}
