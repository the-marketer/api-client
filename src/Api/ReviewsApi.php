<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\Common\AbstractApi;
use TheMarketer\ApiClient\DTO\MerchantPro\MerchantProSettings;
use TheMarketer\ApiClient\DTO\Reviews\AddReview;
use TheMarketer\ApiClient\DTO\Reviews\MerchantAddReview;
use TheMarketer\ApiClient\DTO\Reviews\ProductReviews;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\ApiGateway;

class ReviewsApi extends AbstractApi
{
    /**
     * @param  array<string, mixed>  $query
     * @return string - return XML
     *
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws ValidationException
     * @throws JsonException
     * @throws GuzzleException
     */
    public function get(array $query = []): string
    {
        $dto = ProductReviews::validateAndCreate($query);

        $response = $this->context->http->get('/product_reviews', $dto->toApiPayload(), json: false);
        return $response->getBody()->getContents();
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
        $dto = AddReview::validateAndCreate($payload);

        return $this->context->http->post('/add_review', $dto->toApiPayload());
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

        return $this->context->http->post('/merchant_add_review', $dto->toApiPayload());
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

        return $this->context->http->post('/merchantpro_settings', $dto->toApiPayload());
    }
}
