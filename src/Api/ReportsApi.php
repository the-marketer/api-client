<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\Common\AbstractApi;
use TheMarketer\ApiClient\DTO\Reports\Audience;
use TheMarketer\ApiClient\DTO\Reports\FormsReports;
use TheMarketer\ApiClient\DTO\Reports\EmailReports;
use TheMarketer\ApiClient\DTO\Reports\PushReports;
use TheMarketer\ApiClient\DTO\Reports\SmsReports;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\ApiGateway;

class ReportsApi extends AbstractApi
{
    private const REPORTS_PATH_PREFIX = '/reports';

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
        $dto = EmailReports::validateAndCreate($query);

        return $this->context->http->get(self::REPORTS_PATH_PREFIX . '/get-email-campaigns', $dto->toApiPayload());
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
        $dto = EmailReports::validateAndCreate($query);

        return $this->context->http->get(self::REPORTS_PATH_PREFIX . '/get-email-automation', $dto->toApiPayload());
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
        $dto = PushReports::validateAndCreate($query);

        return $this->context->http->get(self::REPORTS_PATH_PREFIX . '/get-push-campaigns', $dto->toApiPayload());
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
        $dto = PushReports::validateAndCreate($query);

        return $this->context->http->get(self::REPORTS_PATH_PREFIX . '/get-push-automation', $dto->toApiPayload());
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
        $dto = SmsReports::validateAndCreate($query);

        return $this->context->http->get(self::REPORTS_PATH_PREFIX . '/get-sms-campaigns', $dto->toApiPayload());
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
        $dto = SmsReports::validateAndCreate($query);

        return $this->context->http->get(self::REPORTS_PATH_PREFIX . '/get-sms-automation', $dto->toApiPayload());
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
        $dto = FormsReports::validateAndCreate($query);

        return $this->context->http->get(self::REPORTS_PATH_PREFIX . '/get-forms-popups', $dto->toApiPayload());
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
        $dto = FormsReports::validateAndCreate($query);

        return $this->context->http->get(self::REPORTS_PATH_PREFIX . '/get-forms-embedded', $dto->toApiPayload());
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
        $dto = Audience::validateAndCreate($query);

        return $this->context->http->get(self::REPORTS_PATH_PREFIX . '/get-audience', $dto->toApiPayload());
    }
}
