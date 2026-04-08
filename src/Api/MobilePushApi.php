<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\Common\AbstractApi;
use TheMarketer\ApiClient\DTO\AppPush\RemoveMobilePushToken;
use TheMarketer\ApiClient\DTO\AppPush\SetMobilePushToken;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;

class MobilePushApi extends AbstractApi
{
    private const MOBILE_PUSH_URL = '/app-push-notifications/token';

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
    public function setToken(string $email, string $token, string $type): array
    {
        $dto = SetMobilePushToken::validateAndCreate([
            'email' => $email,
            'token' => $token,
            'type' => $type,
        ]);

        return $this->context->http->post(self::MOBILE_PUSH_URL . '/set', $dto->toApiPayload());
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
    public function removeToken(string $email, string $type): array
    {
        $dto = RemoveMobilePushToken::validateAndCreate([
            'email' => $email,
            'type' => $type,
        ]);

        return $this->context->http->post(self::MOBILE_PUSH_URL . '/remove', $dto->toApiPayload());
    }
}
