<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\Common\AbstractApi;
use TheMarketer\ApiClient\DTO\Campaigns\CampaignId;
use TheMarketer\ApiClient\DTO\Campaigns\CreateCampaign;
use TheMarketer\ApiClient\DTO\Campaigns\LatestCampaign;
use TheMarketer\ApiClient\DTO\Campaigns\ListCampaign;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;

class CampaignsApi extends AbstractApi
{
    private const CAMPAIGNS_ENDPOINT = '/campaigns';

    /**
     * @return array<string, mixed>
     *
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws ValidationException
     * @throws JsonException
     * @throws GuzzleException
     */
    public function list(array $payload = []): array
    {
        $dto = ListCampaign::validateAndCreate($payload ?? []);

        return $this->context->http->post(self::CAMPAIGNS_ENDPOINT . '/list', $dto->toApiPayload());
    }

    /**
     * @return array<string, mixed>
     *
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws ValidationException
     * @throws JsonException
     * @throws GuzzleException
     */
    public function create(array $payload): array
    {
        $dto = CreateCampaign::validateAndCreate($payload);

        return $this->context->http->post(self::CAMPAIGNS_ENDPOINT . '/create', $dto->toApiPayload());
    }

    /**
     * @return array<string, mixed>
     *
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws ValidationException
     * @throws JsonException
     * @throws GuzzleException
     */
    public function getEmailReport(string $id): array
    {
        $dto = CampaignId::validateAndCreate(['id' => $id]);

        return $this->context->http->get(self::CAMPAIGNS_ENDPOINT . '/' . $dto->id . '/email/get-report');
    }

    /**
     * @return array<string, mixed>|list<array<string, mixed>>
     *
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws ValidationException
     * @throws JsonException
     * @throws GuzzleException
     */
    public function getLatestCampaign(?int $limit = null): array
    {
        $dto = LatestCampaign::validateAndCreate(['limit' => $limit]);

        return $this->context->http->get('/get-latest-campaign', $dto->toApiPayload());
    }
}
