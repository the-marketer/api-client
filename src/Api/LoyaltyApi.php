<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\DTO\Loyalty\ManageLoyaltyPoints;
use TheMarketer\ApiClient\DTO\Subscribers\SubscriberEmail;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\ApiGateway;

class LoyaltyApi
{
    public function __construct(
        private readonly ApiGateway $api,
    ) {
    }

    /**
     * GET `/loyalty_info` — query: `email` (validat).
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
    public function getInfo(string $email): array
    {
        $query = SubscriberEmail::validateAndCreate([
            'email' => $email,
        ])->toArray();

        $request = $this->api->getRequest('/loyalty_info', $query);
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
    public function managePoints(string $email, string $action, int $points): array
    {
        $dto = ManageLoyaltyPoints::validateAndCreate([
            'email' => $email,
            'action' => $action,
            'points' => $points,
        ])->toArray();

        $request = $this->api->postRequest('/manage_loyalty_points', $dto);
        return $this->api->decodeJson($this->api->sendJson($request));
    }
}
