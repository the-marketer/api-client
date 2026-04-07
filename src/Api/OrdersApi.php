<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\Common\AbstractApi;
use TheMarketer\ApiClient\DTO\Orders\SaveOrder;
use TheMarketer\ApiClient\DTO\Orders\SaveOrderRetail;
use TheMarketer\ApiClient\DTO\Orders\UpdateFeedUrl;
use TheMarketer\ApiClient\DTO\Orders\UpdateOrderStatus;
use TheMarketer\ApiClient\Exception\ValidationException;

class OrdersApi extends AbstractApi
{
    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function updateOrderStatus(string $order_number, string $order_status): array
    {
        $dto = UpdateOrderStatus::validateAndCreate([
            'order_number' => $order_number,
            'order_status' => $order_status,
        ]);

        return $this->context->http->get('/update_order_status', $dto->toApiPayload());
    }

    /**
     * Alias pentru {@see saveOrder()}.
     *
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     *
     * @throws ValidationException
     * @throws JsonException
     * @throws GuzzleException
     */
    public function save(array $payload): array
    {
        return $this->saveOrder($payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     *
     * @throws ValidationException
     * @throws JsonException
     * @throws GuzzleException
     */
    public function saveOrder(array $payload): array
    {
        $dto = SaveOrder::validateAndCreate($payload);

        return $this->context->http->post('/save_order', $dto->toApiPayload());
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function saveOrderRetail(array $payload): array
    {
        $dto = SaveOrderRetail::validateAndCreate($payload);

        return $this->context->http->post('/save_order_retail', $dto->toApiPayload());
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function updateFeedUrl(string $url, ?string $type = null): array
    {
        $dto = UpdateFeedUrl::validateAndCreate([
            'url' =>  $url,
            'type' => $type
        ]);

        return $this->context->http->post('/update_feed_url', $dto->toApiPayload());
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function updateOrderFeedUrl(string $url, ?string $type = null): array
    {
        $dto = UpdateFeedUrl::validateAndCreate([
            'url' =>  $url,
            'type' => $type
        ]);

        return $this->context->http->post('/update_order_feed_url', $dto->toApiPayload());
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getEcommerceStats(): array
    {
        return $this->context->http->get('/get-ecommerce-stats');
    }
}
