<?php

declare(strict_types=1);

namespace NotificationService\Sdk\Internal;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TheMarketer\ApiClient\Common\AbstractApi;
use TheMarketer\ApiClient\DTO\Loyalty\ManageLoyaltyPoints;
use TheMarketer\ApiClient\DTO\Subscribers\EmailValidator;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;

class LoyaltyApi extends AbstractApi
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
    public function getInfo(string $email): array
    {
        $dto = EmailValidator::validateAndCreate(['email' => $email]);

        return $this->context->rest->get('/loyalty_info', $dto->toApiPayload());
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
    public function managePoints(string $email, string $action, int $points): array
    {
        $dto = ManageLoyaltyPoints::validateAndCreate([
            'email' => $email,
            'action' => $action,
            'points' => $points,
        ]);

        return $this->context->rest->post('/manage_loyalty_points', $dto->toApiPayload());
    }
}
