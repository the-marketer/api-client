<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use NotificationService\Sdk\Internal\AppPushApi;
use NotificationService\Sdk\Internal\CampaignsApi;
use NotificationService\Sdk\Internal\CouponsApi;
use NotificationService\Sdk\Internal\CredentialsClient;
use NotificationService\Sdk\Internal\EventsApi;
use NotificationService\Sdk\Internal\LoyaltyApi;
use NotificationService\Sdk\Internal\OrdersApi;
use NotificationService\Sdk\Internal\ProductsApi;
use NotificationService\Sdk\Internal\ReportsApi;
use NotificationService\Sdk\Internal\ReviewsApi;
use NotificationService\Sdk\Internal\SubscribersApi;
use NotificationService\Sdk\Internal\TransactionalsApi;
use TheMarketer\ApiClient\Common\ApiContext;
use TheMarketer\ApiClient\Common\Config;
use TheMarketer\ApiClient\Exception\ValidationException;

class Client
{
    private SubscribersApi $subscribers;

    private OrdersApi $orders;

    private TransactionalsApi $transactionals;

    private ProductsApi $products;

    private CampaignsApi $campaigns;

    private LoyaltyApi $loyalty;

    private CouponsApi $coupons;

    private ReviewsApi $reviews;

    private AppPushApi $appPush;

    private EventsApi $events;

    private ReportsApi $reports;

    private CredentialsClient $credentials;

    private readonly ApiContext $context;

    /**
     * @param string $customerId
     * @param string $restKey
     * @param int $maxRetryAttempts Extra HTTP attempts after the first try for transient errors (connection, timeout, 502/503/504, etc.). 0 disables retries.
     */
    public function __construct(
        string $customerId,
        string $restKey,
        int $maxRetryAttempts = 1,
    )
    {
        $config = new Config($customerId, $restKey);
        $this->context = new ApiContext(new ApiGateway($config, $maxRetryAttempts), $config);

        $this->subscribers = new SubscribersApi($this->context);

        $this->orders = new OrdersApi($this->context);

        $this->transactionals = new TransactionalsApi($this->context);

        $this->products = new ProductsApi($this->context);

        $this->campaigns = new CampaignsApi($this->context);

        $this->loyalty = new LoyaltyApi($this->context);

        $this->coupons = new CouponsApi($this->context);

        $this->reviews = new ReviewsApi($this->context);

        $this->appPush = new AppPushApi($this->context);

        $this->events = new EventsApi($this->context);

        $this->reports = new ReportsApi($this->context);

        $this->credentials = new CredentialsClient(
            $this->context
        );
    }

    public function subscribers(): SubscribersApi
    {
        return $this->subscribers;
    }

    public function orders(): OrdersApi
    {
        return $this->orders;
    }

    public function transactionals(): TransactionalsApi
    {
        return $this->transactionals;
    }

    public function products(): ProductsApi
    {
        return $this->products;
    }

    public function campaigns(): CampaignsApi
    {
        return $this->campaigns;
    }

    public function loyalty(): LoyaltyApi
    {
        return $this->loyalty;
    }

    public function coupons(): CouponsApi
    {
        return $this->coupons;
    }

    public function reviews(): ReviewsApi
    {
        return $this->reviews;
    }

    public function appPush(): AppPushApi
    {
        return $this->appPush;
    }

    public function events(): EventsApi
    {
        return $this->events;
    }

    public function reports(): ReportsApi
    {
        return $this->reports;
    }

    public function config(): Config
    {
        return $this->context->config;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws ValidationException|GuzzleException|JsonException
     */
    public function checkCredentials(string $trackingKey): array
    {
        return $this->credentials->checkCredentials($trackingKey);
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function checkApiCredentials(): array
    {
        return $this->credentials->checkApiCredentials();
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getCosts(): array
    {
        return $this->credentials->getCosts();
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getRealtimeVisitors(): array
    {
        return $this->credentials->getRealtimeVisitors();
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getSmsCredit(): array
    {
        return $this->credentials->getSmsCredit();
    }

    /**
     * @throws ValidationException
     * @throws GuzzleException|JsonException
     */
    public function getReferralLink(?string $email = null): string
    {
        return $this->credentials->getReferralLink($email);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getDeliveryLogs(array $payload): array
    {
        return $this->credentials->getDeliveryLogs($payload);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getEnteredAutomation(array $payload): array
    {
        return $this->credentials->getEnteredAutomation($payload);
    }
}
