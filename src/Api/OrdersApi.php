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

        return $this->context->rest->get('/update_order_status', $dto->toApiPayload());
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

        return $this->context->rest->post('/save_order', $dto->toApiPayload());
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function saveOrderRetail(array $payload): array
    {
        $dto = SaveOrderRetail::validateAndCreate($payload);

        return $this->context->rest->post('/save_order_retail', $dto->toApiPayload());
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

        return $this->context->rest->post('/update_feed_url', $dto->toApiPayload());
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

        return $this->context->rest->post('/update_order_feed_url', $dto->toApiPayload());
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getEcommerceStats(): array
    {
        return $this->context->rest->get('/get-ecommerce-stats');
    }
}
