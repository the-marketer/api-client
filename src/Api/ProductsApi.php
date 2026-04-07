<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\Common\AbstractApi;
use TheMarketer\ApiClient\DTO\Products\CreateProduct;
use TheMarketer\ApiClient\DTO\Products\SyncBrand;
use TheMarketer\ApiClient\DTO\Products\SyncCategory;
use TheMarketer\ApiClient\DTO\Products\UpdateProduct;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;

class ProductsApi extends AbstractApi
{
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
    public function createProduct(array $payload): array
    {
        $dto = CreateProduct::validateAndCreate($payload);

        return $this->context->http->post('/product/create', $dto->toApiPayload());
    }

    /**
     * @param  array<string, mixed>  $payload
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
    public function updateProduct(array $payload): array
    {
        $dto = UpdateProduct::validateAndCreate($payload);

        return $this->context->http->post('/product/update', $dto->toApiPayload());
    }


    /**
     * @param  array<string, mixed>  $payload
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
    public function syncCategories(array $payload): array
    {
        $dto = SyncCategory::validateAndCreate($payload);

        return $this->context->http->post('/category/upsert', $dto->toApiPayload());
    }

    /**
     * @param  array<string, mixed>  $payload
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
    public function syncBrands(array $payload): array
    {
        $dto = SyncBrand::validateAndCreate($payload);

        return $this->context->http->post('/brand/upsert', $dto->toApiPayload());
    }
}
