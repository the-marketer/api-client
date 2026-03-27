<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\ClientInterface;
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
use TheMarketer\ApiClient\HttpClient;

class ProductsApi extends HttpClient
{
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
    public function createProduct(array $payload): array
    {
        $dto = CreateProduct::validateAndCreate($payload);

        $request = $this->postRequest('/product/create', $dto->toApiPayload());
        return $this->decodeJson($this->sendJson($request));
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

        $request = $this->postRequest('/product/update', $dto->toApiPayload());
        return $this->decodeJson($this->sendJson($request));
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

        $request = $this->postRequest('/category/upsert', $dto);
        return $this->decodeJson($this->sendJson($request));
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

        $request = $this->postRequest('/brand/upsert', $dto);
        return $this->decodeJson($this->sendJson($request));
    }
}
