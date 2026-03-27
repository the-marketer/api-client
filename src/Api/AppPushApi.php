<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\DTO\AppPush\RemoveAppPushToken;
use TheMarketer\ApiClient\DTO\AppPush\SetAppPushToken;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\HttpClient;

class AppPushApi extends HttpClient
{
    private const APP_PUSH_URL = '/app-push-notifications/token';
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
    public function setToken(string $email, string $token, string $type): array
    {
        $dto = SetAppPushToken::validateAndCreate([
            'email' => $email,
            'token' => $token,
            'type' => $type,
        ])->toArray();

        $request = $this->postRequest(self::APP_PUSH_URL . '/set', $dto);
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
    public function removeToken(string $email, string $type): array
    {
        $dto = RemoveAppPushToken::validateAndCreate([
            'email' => $email,
            'type' => $type,
        ]);

        $request = $this->postRequest(self::APP_PUSH_URL . '/remove', $dto->toArray());
        return $this->decodeJson($this->sendJson($request));
    }
}
