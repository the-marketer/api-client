<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Products;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;

class SyncCategory extends Data
{
    public function __construct(
        #[Required]
        public string $id,
        #[Required]
        public string $name,
        #[Required]
        public string $hierarchy,
        #[Required]
        #[Rule('url')]
        public string $url,
        #[Required]
        #[Rule('url')]
        public string $image_url,
    ) {
    }
}
