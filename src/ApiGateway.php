<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;

final class ApiGateway
{
    private const USER_AGENT = 'TheMarketer API Client';

    private readonly GuzzleClient $client;

    /**
     * @param GuzzleClient|null $client Opțional: client Guzzle (ex. mock în teste). Dacă e null, se creează client cu retry.
     */
    public function __construct(
        private readonly Config $config,
        int $maxRetryAttempts = 1,
        ?GuzzleClient $client = null,
    )
    {
        $this->client = $client ?? $this->createClient($maxRetryAttempts);
    }

    /**
     * @throws GuzzleException
     * @throws ValidationException
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws JsonException
     */
    public function get(string $endpoint, array $query = [], bool $json = true): ResponseInterface|array
    {
        return $this->request('GET', $endpoint, [], $query, $json);
    }

    /**
     * @throws GuzzleException
     * @throws ValidationException
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws JsonException
     */
    public function post(string $endpoint, array $data = [], array $query = [], bool $json = true): ResponseInterface|array
    {
        return $this->request('POST', $endpoint, $data, $query, $json);
    }

    /**
     * @throws GuzzleException
     * @throws ValidationException
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws JsonException
     */
    public function put(string $endpoint, array $data = [], array $query = [], bool $json = true): ResponseInterface|array
    {
        return $this->request('PUT', $endpoint, $data, $query, $json);
    }

    /**
     * @throws GuzzleException
     * @throws ValidationException
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws JsonException
     */
    public function patch(string $endpoint, array $data = [], array $query = [], bool $json = true): ResponseInterface|array
    {
        return $this->request('PATCH', $endpoint, $data, $query, $json);
    }

    /**
     * @throws GuzzleException
     * @throws ValidationException
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws JsonException
     */
    public function delete(string $endpoint, array $data = [], array $query = [], bool $json = true): ResponseInterface|array
    {
        return $this->request('DELETE', $endpoint, $data, $query, $json);
    }

    public function isSuccessful(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    /**
     * @throws JsonException
     */
    public function decodeJson(ResponseInterface $response): array
    {
        $body = (string)$response->getBody();
        if ($body === '') {
            return [];
        }

        $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }

    public function config(): Config
    {
        return $this->config;
    }

    /**
     * @throws GuzzleException
     * @throws ValidationException
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws JsonException
     */
    private function request(string $method, string $endpoint, array $data, array $query, bool $json): ResponseInterface|array
    {
        $this->assertDomainAuthPresent();

        $options = ['query' => array_merge($this->authQuery(), $query)];
        if ($data !== []) {
            $options['json'] = $data;
        }

//        $response = $this->client->request($method, $this->config->baseUrl() . $endpoint, $options);
        $response = $this->client->request($method, $this->config->apiUrl() . $endpoint, $options);

        $this->throwForErrorResponse($response);

        return $json ? $this->decodeJson($response) : $response;
    }

    private function authQuery(): array
    {
        return [
            'k' => $this->config->restKey(),
            'u' => $this->config->customerId(),
        ];
    }

    /**
     * @throws ValidationException
     */
    private function assertDomainAuthPresent(): void
    {
        if ($this->config->customerId() === '') {
            throw new ValidationException('Customer ID not provided.');
        }

        if ($this->config->restKey() === '') {
            throw new ValidationException('Rest key not provided.');
        }
    }

    /**
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     */
    private function throwForErrorResponse(ResponseInterface $response): void
    {
        if ($this->isSuccessful($response)) {
            return;
        }

        $status = $response->getStatusCode();
        $message = $this->extractErrorMessage($response);

        match ($status) {
            401 => throw new UnauthorizedException($message),
            404 => throw new CustomerNotFoundException($message),
            405 => throw new MethodNotAllowedException($message),
            default => throw new ApiException($message, $status),
        };
    }

    private function extractErrorMessage(ResponseInterface $response): string
    {
        $body = (string)$response->getBody();
        if ($body === '') {
            return 'Request failed';
        }

        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            if (is_array($decoded)) {
                return is_string($decoded['message'] ?? null) ? $decoded['message'] : '';
            }
        } catch (JsonException) {
            // fall through
        }

        return mb_strlen($body) > 500 ? mb_substr($body, 0, 500) . '…' : $body;
    }

    private function createClient(int $maxRetryAttempts): GuzzleClient
    {
        return new GuzzleClient([
            'handler' => GuzzleRetryHandlerStackFactory::create(null, $maxRetryAttempts),
            'http_errors' => false,
            'headers' => [
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }
}