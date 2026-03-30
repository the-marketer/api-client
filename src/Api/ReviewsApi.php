<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\DTO\MerchantPro\MerchantProSettings;
use TheMarketer\ApiClient\DTO\Reviews\AddReview;
use TheMarketer\ApiClient\DTO\Reviews\MerchantAddReview;
use TheMarketer\ApiClient\DTO\Reviews\ProductReviewsQuery;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\ApiGateway;

class ReviewsApi
{
    public function __construct(
        private readonly ApiGateway $api,
    ) {
    }

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
    public function get(array $query = []): array
    {
        $dto = ProductReviewsQuery::validateAndCreate($query);

        $request = $this->api->getRequest('/product_reviews', $dto->toApiPayload());
        return $this->api->decodeJson($this->api->sendJson($request));
    }

    /**
     * @param  array<string, mixed>  $payload
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
    public function create(array $payload): array
    {
        $dto = AddReview::validateAndCreate($payload)->toArray();

        $request = $this->api->postRequest('/add_review', $dto);
        return $this->api->decodeJson($this->api->sendJson($request));
    }

    /**
     * @param  array<string, mixed>  $payload
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
    public function merchantAddReview(array $payload): array
    {
        $dto = MerchantAddReview::validateAndCreate($payload);

        $request = $this->api->postRequest('/merchant_add_review', $dto->toApiPayload());
        return $this->api->decodeJson($this->api->sendJson($request));
    }

    /**
     * @param  array<string, mixed>  $payload
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
    public function merchantProSetting(array $payload = []): array
    {
        $dto = MerchantProSettings::validateAndCreate($payload);

        $request = $this->api->postRequest('/merchantpro_settings', $dto->toApiPayload());
        return $this->api->decodeJson($this->api->sendJson($request));
    }
}
