<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use TheMarketer\ApiClient\DTO\Credentials\CheckCredentialsQuery;
use TheMarketer\ApiClient\DTO\Credentials\GetDeliveryLogsQuery;
use TheMarketer\ApiClient\DTO\Credentials\GetEnteredAutomationQuery;
use TheMarketer\ApiClient\DTO\Credentials\GetReferralLinkQuery;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\ApiGateway;

final class CredentialsClient
{
    public function __construct(
        private readonly ApiGateway $http,
        private readonly ?string $trackingKey = null,
    ) {
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
            'r' => $this->http->config()->restKey(),
            'u' => $this->http->config()->customerId(),
        ]);

        $request = $this->http->postRequest(
            '/check-credentials',
            [],
            $dto->toApiPayload(),
            $this->checkCredentialsHeaders(),
            false,
        );

        return $this->http->decodeJson($this->http->sendJson($request));
    }

    /**
     * Override in a subclass to send extra headers for `/check-credentials` only.
     *
     * @return array<string, string>
     */
    private function checkCredentialsHeaders(): array
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
        $request = $this->http->postRequest('/check-api-credentials', []);

        return $this->http->decodeJson($this->http->sendJson($request));
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getCosts(): array
    {
        $request = $this->http->getRequest('/get_costs');
        return $this->http->decodeJson($this->http->sendJson($request));
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getRealtimeVisitors(): array
    {
        $request = $this->http->getRequest('/realtime_visitors');
        return $this->http->decodeJson($this->http->sendJson($request));
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getSmsCredit(): array
    {
        $request = $this->http->getRequest('/check-sms-credit');
        return $this->http->decodeJson($this->http->sendJson($request));
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

        $request = $this->http->getRequest('/get-referral-link', $query);

        return (string) $this->http->sendJson($request)->getBody();
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getDeliveryLogs(array $payload): array
    {
        $dto = GetDeliveryLogsQuery::validateAndCreate($payload);

        $request = $this->http->getRequest('/delivery-logs', $dto->toApiPayload());
        return $this->http->decodeJson($this->http->sendJson($request));
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

            $request = $this->http->getRequest('/entered-automation', $dto->toApiPayload());
            return $this->http->decodeJson($this->http->sendJson($request));
        } catch (\InvalidArgumentException $e) {
            throw new ValidationException($e->getMessage(), 422);
        }
    }
}
