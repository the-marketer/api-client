<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\DTO\AppPush\RemoveAppPushToken;
use TheMarketer\ApiClient\DTO\AppPush\SetAppPushToken;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\ApiGateway;

class AppPushApi
{
    private const APP_PUSH_URL = '/app-push-notifications/token';

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
    public function setToken(string $email, string $token, string $type): array
    {
        $dto = SetAppPushToken::validateAndCreate([
            'email' => $email,
            'token' => $token,
            'type' => $type,
        ])->toArray();

        $request = $this->api->postRequest(self::APP_PUSH_URL . '/set', $dto);
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
    public function removeToken(string $email, string $type): array
    {
        $dto = RemoveAppPushToken::validateAndCreate([
            'email' => $email,
            'type' => $type,
        ]);

        $request = $this->api->postRequest(self::APP_PUSH_URL . '/remove', $dto->toArray());
        return $this->api->decodeJson($this->api->sendJson($request));
    }
}
