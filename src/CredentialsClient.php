<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use TheMarketer\ApiClient\DTO\Credentials\CheckCredentialsQuery;
use TheMarketer\ApiClient\DTO\Credentials\GetDeliveryLogsQuery;
use TheMarketer\ApiClient\DTO\Credentials\GetEnteredAutomationQuery;
use TheMarketer\ApiClient\DTO\Credentials\GetReferralLinkQuery;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;

final class CredentialsClient extends HttpClient
{
    public function __construct(
        ClientInterface $httpClient,
        ?string $domainApiKey,
        ?string $domainKey,
        ?string $baseUrl = null,
        private readonly ?string $trackingKey = null,
    ) {
        parent::__construct($httpClient, $domainKey, $domainApiKey, $baseUrl);
    }

    /**
     * @return array<string, mixed>|list<mixed>
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function checkCredentials(): array
    {
        $dto = CheckCredentialsQuery::validateAndCreate([
            'k' => $this->trackingKey,
            'r' => $this->domainApiKey,
            'u' => $this->domainKey,
        ]);

        $request = $this->postRequest(
            '/check-credentials',
            [],
            $dto->toApiPayload(),
            $this->checkCredentialsHeaders(),
            false,
        );

        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * Override in a subclass to send extra headers for `/check-credentials` only.
     *
     * @return array<string, string>
     */
    protected function checkCredentialsHeaders(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>|list<mixed>
     *
     * On success, returns the decoded JSON body. On failure (e.g. 404, 422), the client throws
     * {@see \TheMarketer\ApiClient\Exception\ApiException} (or a more specific exception) with the API `message` from the body.
     *
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function checkApiCredentials(): array
    {
        $request = $this->postRequest('/check-api-credentials', []);

        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getCosts(): array
    {
        $request = $this->getRequest('/get_costs');
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getRealtimeVisitors(): array
    {
        $request = $this->getRequest('/realtime_visitors');
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getSmsCredit(): array
    {
        $request = $this->getRequest('/check-sms-credit');
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @throws ValidationException
     * @throws GuzzleException
     */
    public function getReferralLink(?string $email = null): string
    {
        $dto = GetReferralLinkQuery::validateAndCreate([
            'email' => $email,
        ]);

        $query = [];
        if ($dto->email !== null) {
            $query['email'] = $dto->email;
        }

        $request = $this->getRequest('/get-referral-link', $query);

        return (string) $this->sendJson($request)->getBody();
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getDeliveryLogs(array $payload): array
    {
        $dto = GetDeliveryLogsQuery::validateAndCreate($payload);

        $request = $this->getRequest('/delivery-logs', $dto->toApiPayload());
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getEnteredAutomation(array $payload): array
    {
        try {
            $dto = GetEnteredAutomationQuery::validateAndCreate($payload);

            $request = $this->getRequest('/entered-automation', $dto->toApiPayload());
            return $this->decodeJson($this->sendJson($request));
        } catch (\InvalidArgumentException $e) {
            throw new ValidationException($e->getMessage(), 422);
        }
    }
}
