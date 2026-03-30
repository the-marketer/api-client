<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\DTO\Transactionals\SendEmail;
use TheMarketer\ApiClient\DTO\Transactionals\SendSms;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\ApiGateway;

class TransactionalsApi
{
    public function __construct(
        private readonly ApiGateway $api,
    ) {
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
    public function sendEmail(array $payload): array {
        $dto = SendEmail::validateAndCreate($payload);

        $request = $this->api->postRequest('/transactional/send-email', $dto->toApiPayload());
        return $this->api->decodeJson($this->api->sendJson($request));
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
        ])->toArray();

        $request = $this->api->postRequest('/transactional/send-sms', $dto);
        return $this->api->decodeJson($this->api->sendJson($request));
    }
}