<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Credentials;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class CheckCredentials extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $k,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $r,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $u,
    ) {}
}
