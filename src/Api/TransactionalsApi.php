<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\Common\AbstractApi;
use TheMarketer\ApiClient\DTO\Transactionals\SendEmail;
use TheMarketer\ApiClient\DTO\Transactionals\SendEmailsBulk;
use TheMarketer\ApiClient\DTO\Transactionals\SendSms;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;

class TransactionalsApi extends AbstractApi
{
    private const TRANSACTIONAL_ENDPOINT = '/transactional';
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
    public function sendEmail(array $payload): array
    {
        $dto = SendEmail::validateAndCreate($payload);

        return $this->context->http->post(self::TRANSACTIONAL_ENDPOINT . '/send-email', $dto->toApiPayload());
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
    public function sendSms(string $to, string $content): array
    {
        $dto = SendSms::validateAndCreate([
            'to' => $to,
            'content' => $content,
        ]);

        return $this->context->http->post(self::TRANSACTIONAL_ENDPOINT . '/send-sms', $dto->toApiPayload());
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function sendEmailAsync(array $payload): array
    {
        $dto = SendEmail::validateAndCreate($payload);

        return $this->context->http->post(self::TRANSACTIONAL_ENDPOINT . '/queue-send-email', $dto->toApiPayload());
    }

    /**
     * @param array{emails: list<array<string, mixed>>} $payload
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
    public function sendEmailsBulk(array $payload): array
    {
        $dto = SendEmailsBulk::validateAndCreate($payload);

        return $this->context->http->post(self::TRANSACTIONAL_ENDPOINT . '/batch-send-email', $dto->toApiPayload());
    }
}