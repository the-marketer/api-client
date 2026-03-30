<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\Common\ApiContext;
use TheMarketer\ApiClient\DTO\Subscribers\AddSubscriberBulk;
use TheMarketer\ApiClient\DTO\Subscribers\AddSubscriberByPhone;
use TheMarketer\ApiClient\DTO\Subscribers\DeleteSubscriber;
use TheMarketer\ApiClient\DTO\Subscribers\ListSubscribersDateRange;
use TheMarketer\ApiClient\DTO\Subscribers\RemoveSubscriber;
use TheMarketer\ApiClient\DTO\Subscribers\SubscriberEmail;
use TheMarketer\ApiClient\DTO\Subscribers\SubscriberRow;
use TheMarketer\ApiClient\DTO\Subscribers\UnsubscribedEmails;
use TheMarketer\ApiClient\DTO\Subscribers\UpdateTags;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;

class SubscribersApi
{
    public function __construct(
        private readonly ApiContext $context
    ) {}

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
    public function statusSubscriber(string $email): array
    {
        $query = SubscriberEmail::validateAndCreate(['email' => $email])->toArray();

        return $this->context->http->get('/status_subscriber', $query);
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

        return $this->context->http->get('/unsubscribed_emails', $query);
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
    public function listUnsubscribed(?string $date_from = null, ?string $date_to = null): array
    {
        $dto = ListSubscribersDateRange::validateAndCreate([
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);

        return $this->context->http->get('/unsubscribed_emails', $dto->toListSubscribersDataRangeApiPayload());
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
    public function listSubscribed(?string $date_from = null, ?string $date_to = null): array
    {
        $dto = ListSubscribersDateRange::validateAndCreate([
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);

        return $this->context->http->get('/subscribed_emails', $dto->toListSubscribersDataRangeApiPayload());
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
    public function subscribersEvolution(): array
    {
        return $this->context->http->get('/subscribers-evolution');
    }

    /**
     * Alias pentru {@see addSubscriber()}.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function add(array $payload): array
    {
        return $this->addSubscriber($payload);
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
    public function addSubscriber(array $payload): array
    {
        $dto = SubscriberRow::validateAndCreate($payload);

        return $this->context->http->post('/add_subscriber', $dto->toSubscribersApiPayload());
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
    ): array
    {
        $payload = ['phone' => $phone];
        if ($firstname !== null && $firstname !== '') {
            $payload['firstname'] = $firstname;
        }
        if ($lastname !== null && $lastname !== '') {
            $payload['lastname'] = $lastname;
        }

        $dto = AddSubscriberByPhone::validateAndCreate($payload);

        return $this->context->http->post('/add_subscriber_by_phone', array_replace($payload, ['phone' => $dto->phone]));
    }

    /**
     * @param list<array<string, mixed>> $subscribers
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
        $dto = AddSubscriberBulk::validateAndCreate(['subscribers' => $subscribers]);

        $payload = array_map(
            fn(SubscriberRow $row): array => $row->toSubscribersApiPayload(),
            $dto->subscribers,
        );

        return $this->context->http->post('/add_subscriber_bulk', $payload);
    }

    /**
     * @param array<string, mixed> $payload
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

        return $this->context->http->post('/add_subscriber_sync', $dto->toSubscribersApiPayload());
    }

    /**
     * @param array<string, mixed> $payload `email` și/sau `phone`
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
        $trim = static fn(mixed $v): mixed => is_string($v) ? trim($v) : $v;

        $dto = DeleteSubscriber::validateAndCreate([
            'email' => array_key_exists('email', $payload) ? $trim($payload['email']) : null,
            'phone' => array_key_exists('phone', $payload) ? $trim($payload['phone']) : null,
        ]);

        $body = array_filter(
            ['email' => $dto->email, 'phone' => $dto->phone],
            static fn($v) => $v !== null && $v !== '',
        );

        return $this->context->http->post('/delete_subscriber', $body);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws UnauthorizedException
     * @throws CustomerNotFoundException
     * @throws MethodNotAllowedException
     * @throws ApiException
     * @throws ValidationException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function removeSubscriber(string $email, ?string $channels = null): array
    {
        $dto = RemoveSubscriber::validateAndCreate([
            'email' => $email,
            'channels' => $channels,
        ]);

        $query = array_filter(
            ['email' => $dto->email, 'channels' => $dto->channels],
            static fn($v) => $v !== null && $v !== '',
        );

        return $this->context->http->post('/remove_subscriber', [], $query, true);
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function anonymizeEmail(string $email): array
    {
        $body = SubscriberEmail::validateAndCreate(['email' => $email])->toArray();

        return $this->context->http->post('/anonymize-email', $body);
    }

    /**
     * @param list<string|int> $add_tags
     * @param list<string|int> $remove_tags
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
    ): array
    {
        $dto = UpdateTags::validateAndCreate([
            'email' => $email,
            'add_tags' => $add_tags,
            'remove_tags' => $remove_tags,
            'overwrite_existing' => $overwrite_existing,
        ]);

        return $this->context->http->post('/update-tags', [], $dto->toApiPayload(), true);
    }
}