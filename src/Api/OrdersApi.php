<?php

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use TheMarketer\ApiClient\DTO\Orders\SaveOrder;
use TheMarketer\ApiClient\DTO\Orders\UpdateFeedUrl;
use TheMarketer\ApiClient\DTO\Orders\UpdateOrderStatus;
use TheMarketer\ApiClient\HttpClient;

class OrdersApi extends HttpClient
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
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function updateOrderStatus(string $order_number, string $order_status): array
    {
        $dto = UpdateOrderStatus::validateAndCreate([
            'order_number' => $order_number,
            'order_status' => $order_status,
        ])->toArray();

        $request = $this->getRequest('/update_order_status', $dto);
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    /**
     * Alias pentru {@see saveOrder()}.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function save(array $payload): array
    {
        return $this->saveOrder($payload);
    }

    public function saveOrder(array $payload): array
    {
        $dto = SaveOrder::validateAndCreate($payload);

        $request = $this->postRequest('/save_order', $dto->toApiPayload());
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function saveOrderRetail(array $payload): array {
        $dto = SaveOrder::validateAndCreate($payload);

        $request = $this->postRequest('/save_order_retail', $dto->toApiPayload());
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function updateFeedUrl(string $url, ?string $type = null): array
    {
        $dto = UpdateFeedUrl::fromUrlAndOptionalType($url, $type);

        $request = $this->postRequest('/update_feed_url', $dto->toApiPayload());
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function updateOrderFeedUrl(string $url, ?string $type = null): array
    {
        $dto = UpdateFeedUrl::fromUrlAndOptionalType($url, $type);

        $request = $this->postRequest('/update_order_feed_url', $dto->toApiPayload());
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getEcommerceStats(): array
    {
        $request = $this->getRequest('/get-ecommerce-stats');
        return $this->decodeJson($this->sendJson($request));
    }
}
