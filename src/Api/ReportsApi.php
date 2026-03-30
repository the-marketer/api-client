<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\DTO\Reports\GetAudienceReportsQuery;
use TheMarketer\ApiClient\DTO\Reports\GetFormsReportsQuery;
use TheMarketer\ApiClient\DTO\Reports\GetEmailReportsQuery;
use TheMarketer\ApiClient\DTO\Reports\GetPushReportsQuery;
use TheMarketer\ApiClient\DTO\Reports\GetSmsReportsQuery;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\ApiGateway;

class ReportsApi
{
    private const REPORTS_PATH_PREFIX = '/reports';

    public function __construct(
        private readonly ApiGateway $api,
    ) {
    }

    /**
     * @param  array<string, mixed>  $query
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
    public function getEmailCampaigns(array $query): array
    {
        $dto = GetEmailReportsQuery::validateAndCreate($query)->toArray();

        $request = $this->api->getRequest(self::REPORTS_PATH_PREFIX . '/get-email-campaigns', $dto);
        return $this->api->decodeJson($this->api->sendJson($request));
    }

    /**
     * @param  array<string, mixed>  $query
     *
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
    public function getEmailAutomation(array $query): array
    {
        $dto = GetEmailReportsQuery::validateAndCreate($query)->toArray();

        $request = $this->api->getRequest(self::REPORTS_PATH_PREFIX . '/get-email-automation', $dto);
        return $this->api->decodeJson($this->api->sendJson($request));
    }

    /**
     * @param  array<string, mixed>  $query
     *
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
    public function getPushCampaigns(array $query): array
    {
        $dto = GetPushReportsQuery::validateAndCreate($query)->toArray();

        $request = $this->api->getRequest(self::REPORTS_PATH_PREFIX . '/get-push-campaigns', $dto);
        return $this->api->decodeJson($this->api->sendJson($request));
    }

    /**
     * @param  array<string, mixed>  $query
     *
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
    public function getPushAutomation(array $query): array
    {
        $dto = GetPushReportsQuery::validateAndCreate($query)->toArray();

        $request = $this->api->getRequest(self::REPORTS_PATH_PREFIX . '/get-push-automation', $dto);
        return $this->api->decodeJson($this->api->sendJson($request));
    }

    /**
     * @param  array<string, mixed>  $query
     *
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
    public function getSmsCampaigns(array $query): array
    {
        $dto = GetSmsReportsQuery::validateAndCreate($query)->toArray();

        $request = $this->api->getRequest(self::REPORTS_PATH_PREFIX . '/get-sms-campaigns', $dto);
        return $this->api->decodeJson($this->api->sendJson($request));
    }

    /**
     * @param  array<string, mixed>  $query
     *
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
    public function getSmsAutomation(array $query): array
    {
        $dto = GetSmsReportsQuery::validateAndCreate($query)->toArray();

        $request = $this->api->getRequest(self::REPORTS_PATH_PREFIX . '/get-sms-automation', $dto);
        return $this->api->decodeJson($this->api->sendJson($request));
    }

    /**
     * @param  array<string, mixed>  $query
     *
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
    public function getFormsPopups(array $query): array
    {
        $dto = GetFormsReportsQuery::validateAndCreate($query)->toArray();

        $request = $this->api->getRequest(self::REPORTS_PATH_PREFIX . '/get-forms-popups', $dto);
        return $this->api->decodeJson($this->api->sendJson($request));
    }

    /**
     * @param  array<string, mixed>  $query
     *
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
    public function getFormsEmbedded(array $query): array
    {
        $dto = GetFormsReportsQuery::validateAndCreate($query)->toArray();

        $request = $this->api->getRequest(self::REPORTS_PATH_PREFIX . '/get-forms-embedded', $dto);

        return $this->api->decodeJson($this->api->sendJson($request));
    }

    /**
     * @param  array<string, mixed>  $query
     *
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
    public function getAudience(array $query): array
    {
        $dto = GetAudienceReportsQuery::validateAndCreate($query)->toArray();

        $request = $this->api->getRequest(self::REPORTS_PATH_PREFIX . '/get-audience', $dto);
        return $this->api->decodeJson($this->api->sendJson($request));
    }
}
