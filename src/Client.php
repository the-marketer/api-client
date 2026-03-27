<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\Exception\ValidationException;
use NotificationService\Sdk\Internal\AppPushApi;
use NotificationService\Sdk\Internal\CampaignsApi;
use NotificationService\Sdk\Internal\CouponsApi;
use NotificationService\Sdk\Internal\EventsApi;
use NotificationService\Sdk\Internal\LoyaltyApi;
use NotificationService\Sdk\Internal\OrdersApi;
use NotificationService\Sdk\Internal\ProductsApi;
use NotificationService\Sdk\Internal\ReportsApi;
use NotificationService\Sdk\Internal\ReviewsApi;
use NotificationService\Sdk\Internal\SubscribersApi;
use NotificationService\Sdk\Internal\TransactionalsApi;

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

    private readonly ClientInterface $httpClient;

    private readonly string $domainKey;

    private readonly string $domainApiKey;

    private readonly string $trackingKey;

    private readonly string $baseUrl;

    /**
     * @param  string  $customerId
     * @param  string  $restKey
     * @param  string  $apiKey
     * @param  int  $maxRetryAttempts  Extra HTTP attempts after the first try for transient errors (connection, timeout, 502/503/504, etc.). 0 disables retries.
     */
    public function __construct(
        string $customerId,
        string $restKey,
        string $apiKey,
        int $maxRetryAttempts = 1,
    ) {
        $this->httpClient = new GuzzleClient([
            'handler' => GuzzleRetryHandlerStackFactory::create(null, $maxRetryAttempts),
        ]);
        $this->domainKey = $customerId;
        $this->domainApiKey = $restKey;
        $this->trackingKey = $apiKey;
        $this->baseUrl = HttpClient::DEFAULT_BASE_URL;

        $this->subscribers = new SubscribersApi($this->domainKey, $this->domainApiKey, $this->httpClient, $this->baseUrl);

        $this->orders = new OrdersApi($this->domainKey, $this->domainApiKey, $this->httpClient, $this->baseUrl);

        $this->transactionals = new TransactionalsApi($this->domainKey, $this->domainApiKey, $this->httpClient, $this->baseUrl);

        $this->products = new ProductsApi($this->domainKey, $this->domainApiKey, $this->httpClient, $this->baseUrl);

        $this->campaigns = new CampaignsApi($this->domainKey, $this->domainApiKey, $this->httpClient, $this->baseUrl);

        $this->loyalty = new LoyaltyApi($this->domainKey, $this->domainApiKey, $this->httpClient, $this->baseUrl);

        $this->coupons = new CouponsApi($this->domainKey, $this->domainApiKey, $this->httpClient, $this->baseUrl);

        $this->reviews = new ReviewsApi($this->domainKey, $this->domainApiKey, $this->httpClient, $this->baseUrl);

        $this->appPush = new AppPushApi($this->domainKey, $this->domainApiKey, $this->httpClient, $this->baseUrl);

        $this->events = new EventsApi($this->domainKey, $this->domainApiKey, $this->httpClient, $this->baseUrl);

        $this->reports = new ReportsApi($this->domainKey, $this->domainApiKey, $this->httpClient, $this->baseUrl);

        $this->credentials = new CredentialsClient($this->httpClient, $this->domainApiKey, $this->domainKey, $this->baseUrl, $this->trackingKey);
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

    public function trackingKey(): string
    {
        return $this->trackingKey;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws ValidationException|GuzzleException|JsonException
     */
    public function checkCredentials(): array
    {
        return $this->credentials->checkCredentials();
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
     * @throws GuzzleException
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
     * @param  array<string, mixed>  $payload
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
