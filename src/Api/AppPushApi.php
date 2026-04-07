<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\Common\AbstractApi;
use TheMarketer\ApiClient\DTO\AppPush\RemoveAppPushToken;
use TheMarketer\ApiClient\DTO\AppPush\SetAppPushToken;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;

class AppPushApi extends AbstractApi
{
    private const APP_PUSH_URL = '/app-push-notifications/token';

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
        $dto = SetAppPushToken::validateAndCreate([
            'email' => $email,
            'token' => $token,
            'type' => $type,
        ]);

        return $this->context->http->post(self::APP_PUSH_URL . '/set', $dto->toApiPayload());
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
        $dto = RemoveAppPushToken::validateAndCreate([
            'email' => $email,
            'type' => $type,
        ]);

        return $this->context->http->post(self::APP_PUSH_URL . '/remove', $dto->toApiPayload());
    }
}
