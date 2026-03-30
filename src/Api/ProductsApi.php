<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\DTO\Products\CreateProduct;
use TheMarketer\ApiClient\DTO\Products\SyncBrand;
use TheMarketer\ApiClient\DTO\Products\SyncCategory;
use TheMarketer\ApiClient\DTO\Products\UpdateProduct;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;
use TheMarketer\ApiClient\ApiGateway;

class ProductsApi
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
    public function createProduct(array $payload): array
    {
        $dto = CreateProduct::validateAndCreate($payload);

        $request = $this->api->postRequest('/product/create', $dto->toApiPayload());
        return $this->api->decodeJson($this->api->sendJson($request));
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

        $request = $this->api->postRequest('/product/update', $dto->toApiPayload());
        return $this->api->decodeJson($this->api->sendJson($request));
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
        $dto = SyncCategory::validateAndCreate($payload)->toArray();

        $request = $this->api->postRequest('/category/upsert', $dto);
        return $this->api->decodeJson($this->api->sendJson($request));
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
        $dto = SyncBrand::validateAndCreate($payload)->toArray();

        $request = $this->api->postRequest('/brand/upsert', $dto);
        return $this->api->decodeJson($this->api->sendJson($request));
    }
}
