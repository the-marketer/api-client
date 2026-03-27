<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\DTO\Campaigns\CampaignId;
use TheMarketer\ApiClient\DTO\Campaigns\CreateCampaign;
use TheMarketer\ApiClient\DTO\Campaigns\GetLatestCampaignQuery;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\HttpClient;

class CampaignsApi extends HttpClient
{
    private const CAMPAIGNS_ENDPOINT = '/campaigns';
    public function __construct(
        ?string $domainKey,
        ?string $domainApiKey,
        ClientInterface $httpClient,
        ?string $baseUrl = null
    ) {
        parent::__construct($httpClient, $domainKey, $domainApiKey, $baseUrl);
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
    public function list(): array
    {
        $request = $this->getRequest(self::CAMPAIGNS_ENDPOINT . '/list');
        return $this->decodeJson($this->sendJson($request));
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
        try {
            $dto = CreateCampaign::validateAndCreate($payload);

            $request = $this->postRequest(self::CAMPAIGNS_ENDPOINT . '/create', $dto->toCampaignsApiPayload());
            return $this->decodeJson($this->sendJson($request));
        } catch (\InvalidArgumentException $e) {
            throw new ValidationException($e->getMessage(), 422);
        }
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
    public function getEmailReport(string|int $id): array
    {
        $dto = CampaignId::validateAndCreate([
            'id' => is_int($id) ? (string) $id : $id,
        ]);

        $path = sprintf('/%s/email/get-report', rawurlencode($dto->id));

        $request = $this->getRequest(self::CAMPAIGNS_ENDPOINT . $path);
        return $this->decodeJson($this->sendJson($request));
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
        $query = [];
        if ($limit !== null) {
            $dto = GetLatestCampaignQuery::validateAndCreate(['limit' => $limit]);
            $query['limit'] = $dto->limit;
        }

        $request = $this->getRequest('/get-latest-campaign', $query);
        return $this->decodeJson($this->sendJson($request));
    }
}
