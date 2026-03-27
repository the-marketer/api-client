<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\DTO\Transactionals\SendEmail;
use TheMarketer\ApiClient\DTO\Transactionals\SendSms;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\HttpClient;

class TransactionalsApi extends HttpClient
{
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
    public function sendEmail(array $payload): array {
        $dto = SendEmail::validateAndCreate($payload);

        $request = $this->postRequest('/transactional/send-email', $dto->toApiPayload());
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
    public function sendSms(string $to, string $content): array
    {
        $dto = SendSms::validateAndCreate([
            'to' => $to,
            'content' => $content,
        ])->toArray();

        $request = $this->postRequest('/transactional/send-sms', $dto);
        return $this->decodeJson($this->sendJson($request));
    }
}