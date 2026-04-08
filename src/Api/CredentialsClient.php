<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\Common\AbstractApi;
use TheMarketer\ApiClient\DTO\Credentials\CheckCredentials;
use TheMarketer\ApiClient\DTO\Credentials\DeliveryLogs;
use TheMarketer\ApiClient\DTO\Credentials\EnteredAutomation;
use TheMarketer\ApiClient\DTO\Credentials\ReferralLink;
use TheMarketer\ApiClient\Exception\ValidationException;

final class CredentialsClient extends AbstractApi
{
    /**
     * @return array<string, mixed>|list<mixed>
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function checkCredentials(string $trackingKey): array
    {
        $dto = CheckCredentials::validateAndCreate([
            'k' => $trackingKey,
            'r' => $this->context->config->restKey(),
            'u' => $this->context->config->customerId(),
        ]);

        return $this->context->rest->post('/check-credentials', $dto->toApiPayload());
    }

    /**
     * @return array<string, mixed>|list<mixed>
     *
     * On success, returns the decoded JSON body. On failure (e.g. 404, 422), the client throws
     * {@see \TheMarketer\ApiClient\Exception\ApiException} (or a more specific exception) with the API `message` from the body.
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function checkApiCredentials(): array
    {
        return $this->context->rest->post('/check-api-credentials');
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getCosts(): array
    {
        return $this->context->rest->get('/get_costs');
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getRealtimeVisitors(): array
    {
        return $this->context->rest->get('/realtime_visitors');
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getSmsCredit(): array
    {
        return $this->context->rest->get('/check-sms-credit');
    }

    /**
     * @throws ValidationException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getReferralLink(?string $email = null): string
    {
        $dto = ReferralLink::validateAndCreate(['email' => $email]);

        $response = $this->context->rest->get('/get-referral-link', $dto->toApiPayload(), json: false);
        return $response->getBody()->getContents();
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getDeliveryLogs(array $payload): array
    {
        $dto = DeliveryLogs::validateAndCreate($payload);

        return $this->context->rest->get('/delivery-logs', $dto->toApiPayload());
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getEnteredAutomation(array $payload): array
    {
        $dto = EnteredAutomation::validateAndCreate($payload);

        return $this->context->rest->get('/entered-automation', $dto->toApiPayload());
    }
}
