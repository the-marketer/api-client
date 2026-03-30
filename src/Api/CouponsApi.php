<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\DTO\Coupons\SaveCoupon;
use TheMarketer\ApiClient\DTO\Subscribers\SubscriberEmail;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\ApiGateway;

class CouponsApi
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
    public function getAvailableCoupons(string $email): array
    {
        $query = SubscriberEmail::validateAndCreate([
            'email' => $email,
        ])->toArray();

        $request = $this->api->getRequest('/get_available_coupons', $query);
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
    public function save(array $payload): array
    {
        $dto = SaveCoupon::validateAndCreate($payload)->toArray();

        $request = $this->api->postRequest('/save_coupon', $dto);
        return $this->api->decodeJson($this->api->sendJson($request));
    }
}
