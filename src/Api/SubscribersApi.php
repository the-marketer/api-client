<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\DTO\Subscribers\AddSubscriberBulk;
use TheMarketer\ApiClient\DTO\Subscribers\AddSubscriberByPhone;
use TheMarketer\ApiClient\DTO\Subscribers\DeleteSubscriber;
use TheMarketer\ApiClient\DTO\Subscribers\ListSubscribersDateRange;
use TheMarketer\ApiClient\DTO\Subscribers\RemoveSubscriber;
use TheMarketer\ApiClient\DTO\Subscribers\SubscriberEmail;
use TheMarketer\ApiClient\DTO\Subscribers\SubscriberRow;
use TheMarketer\ApiClient\DTO\Subscribers\UnsubscribedEmails;
use TheMarketer\ApiClient\DTO\Subscribers\UpdateTags;
use TheMarketer\ApiClient\HttpClient;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;

class SubscribersApi extends HttpClient
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
    public function statusSubscriber(string $email): array
    {
        $query = SubscriberEmail::validateAndCreate([
            'email' => $email,
        ])->toArray();

        $request = $this->getRequest('/status_subscriber', $query);
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws ValidationException
     * @throws JsonException
     * @throws GuzzleException
     */
    public function unsubscribedEmails(string $date_from, string $date_to): array
    {
        $query = UnsubscribedEmails::validateAndCreate([
            'date_from' => $date_from,
            'date_to' => $date_to,
        ])->toArray();

        $request = $this->getRequest('/unsubscribed_emails', $query);
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * GET `/unsubscribed_emails` — `date_from` și `date_to` opționale (string).
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
    public function listUnsubscribed(?string $date_from = null, ?string $date_to = null): array
    {
        $dto = ListSubscribersDateRange::validateAndCreate([
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);

        $request = $this->getRequest('/unsubscribed_emails', $dto->toListSubscribersDataRangeApiPayload());
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * GET `/subscribed_emails` — `date_from` și `date_to` opționale (string).
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
    public function listSubscribed(?string $date_from = null, ?string $date_to = null): array
    {
        $dto = ListSubscribersDateRange::validateAndCreate([
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);

        $request = $this->getRequest('/subscribed_emails', $dto->toListSubscribersDataRangeApiPayload());
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * GET `/subscribers-evolution`
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
    public function subscribersEvolution(): array
    {
        $request = $this->getRequest('/subscribers-evolution');
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @param  array<string, mixed>  $payload  `email` obligatoriu; opțional: `add_tags`, `firstname`, `lastname`, `phone`, `city`, `country`, `birthday`, `channels`, `attributes`.
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
    /**
     * Alias pentru {@see addSubscriber()}.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function add(array $payload): array
    {
        return $this->addSubscriber($payload);
    }

    public function addSubscriber(array $payload): array
    {
        $dto = SubscriberRow::validateAndCreate($payload);

        $request = $this->postRequest('/add_subscriber', $dto->toSubscribersApiPayload());
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws ValidationException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function addSubscriberByPhone(
        string $phone,
        ?string $firstname = null,
        ?string $lastname = null,
    ): array {
        $payload = ['phone' => $phone];
        if ($firstname !== null && $firstname !== '') {
            $payload['firstname'] = $firstname;
        }
        if ($lastname !== null && $lastname !== '') {
            $payload['lastname'] = $lastname;
        }

        $dto = AddSubscriberByPhone::validateAndCreate($payload);

        $body = array_replace($payload, ['phone' => $dto->phone]);

        $request = $this->postRequest('/add_subscriber_by_phone', $body);
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     *
     * @param list<array<string, mixed>> $subscribers Each row: `email` required; optional fields match {@see addSubscriber()}.
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
    public function addSubscriberBulk(array $subscribers): array
    {
        $dto = AddSubscriberBulk::validateAndCreate([
            'subscribers' => $subscribers,
        ]);

        $payload = array_map(
            fn (SubscriberRow $row): array => $row->toSubscribersApiPayload(),
            $dto->subscribers,
        );

        $request = $this->postRequest('/add_subscriber_bulk', $payload);

        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @param  array<string, mixed>  $payload  Aceleași chei ca la {@see addSubscriber()} (ex. `email` obligatoriu, rest opționale).
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
    public function addSubscriberSync(array $payload): array
    {
        $dto = SubscriberRow::validateAndCreate($payload);

        $request = $this->postRequest('/add_subscriber_sync', $dto->toSubscribersApiPayload());

        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * POST `/delete_subscriber` — obligatoriu cel puțin unul dintre `email` sau `phone` (validare ca pe backend).
     *
     * @param  array<string, mixed>  $payload  `email` și/sau `phone`
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
    public function deleteSubscriber(array $payload): array
    {
        $trim = static function (mixed $v): mixed {
            return is_string($v) ? trim($v) : $v;
        };

        $payloadNormalized = [
            'email' => array_key_exists('email', $payload) ? $trim($payload['email']) : null,
            'phone' => array_key_exists('phone', $payload) ? $trim($payload['phone']) : null,
        ];

        $dto = DeleteSubscriber::validateAndCreate($payloadNormalized);

        $body = array_filter(
            ['email' => $dto->email, 'phone' => $dto->phone],
            static fn ($v) => $v !== null && $v !== '',
        );

        $request = $this->postRequest('/delete_subscriber', $body);
        return $this->decodeJson($this->sendJson($request));
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
    public function removeSubscriber(string $email, ?string $channels = null): array
    {
        $dto = RemoveSubscriber::validateAndCreate([
            'email' => $email,
            'channels' => $channels,
        ]);

        $query = array_filter(
            ['email' => $dto->email, 'channels' => $dto->channels],
            static fn ($v) => $v !== null && $v !== '',
        );

        $request = $this->postRequest('/remove_subscriber', [], $query);
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function anonymizeEmail(string $email): array
    {
        $body = SubscriberEmail::validateAndCreate([
            'email' => $email,
        ])->toArray();

        $request = $this->postRequest('/anonymize-email', $body);
        return $this->decodeJson($this->sendJson($request));
    }

    /**
     * @param list<string|int> $add_tags
     * @param list<string|int> $remove_tags
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
    public function updateTags(
        string $email,
        array $add_tags = [],
        array $remove_tags = [],
        ?int $overwrite_existing = null,
    ): array {
        $dto = UpdateTags::validateAndCreate([
            'email' => $email,
            'add_tags' => $add_tags,
            'remove_tags' => $remove_tags,
            'overwrite_existing' => $overwrite_existing,
        ]);

        $request = $this->postRequest('/update-tags', [], $dto->toApiPayload());
        return $this->decodeJson($this->sendJson($request));
    }
}
