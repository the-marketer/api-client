<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Credentials;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use TheMarketer\ApiClient\Common\AbstractPayload;

/**
 * Query params for `/check-credentials`: `k` (tracking), `r` (REST key), `u` (customer / domain id).
 * Existence of `u` in storage is validated on the API; the client only enforces presence and non-empty strings.
 */
class CheckCredentialsQuery extends AbstractPayload
{
    public function __construct(
        #[Required]
        #[Rule('string', 'min:1')]
        public string $k,
        #[Required]
        #[Rule('string', 'min:1')]
        public string $r,
        #[Required]
        #[Rule('string', 'min:1')]
        public string $u,
    ) {
    }
}
