<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Events;

use Symfony\Component\Validator\Constraints as Assert;
use TheMarketer\ApiClient\Common\AbstractPayload;

class ServeJavascriptEvent extends AbstractPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 6, max: 20)]
        public string $k,
    ) {
    }

}
