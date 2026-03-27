<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Products;

use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use TheMarketer\ApiClient\Common\AbstractPayload;

#[MergeValidationRules]
class CreateProduct extends AbstractPayload
{
    public function __construct(
        #[Required]
        #[Rule('integer')]
        public int                     $id,
        #[Required]
        #[Rule('string', 'filled')]
        public string                  $sku,
        #[Required]
        #[Rule('string', 'filled')]
        public string                  $name,
        #[Required]
        #[Rule('string', 'filled')]
        public string                  $description,
        #[Required]
        #[Rule('string', 'filled')]
        public string                  $url,
        #[Required]
        #[Rule('string', 'filled')]
        public string                  $main_image,
        #[Required]
        #[Rule('string', 'filled')]
        public string                  $category,
        #[Required]
        #[Rule('string', 'filled')]
        public string                  $brand,
        #[Required]
        #[Numeric]
        public float                   $acquisition_price,
        #[Required]
        #[Numeric]
        public float                   $price,
        #[Required]
        #[Rule('string', 'filled')]
        public string                  $sale_price,
        #[Required]
        #[Rule('integer')]
        public int                     $availability,
        #[Required]
        #[Rule('integer')]
        public int                     $stock,
        #[Required]
        #[Rule('array', 'size:2')]
        public array                   $media_gallery,
        #[Required]
        #[Rule('string', 'filled')]
        public string                  $created_at,
        #[Sometimes]
        public ?ProductExtraAttributes $extra_attributes = null,
        #[Sometimes]
        #[Rule('nullable', 'string')]
        public ?string                 $sale_price_start_date = null,
        #[Sometimes]
        #[Rule('nullable', 'string')]
        public ?string                 $sale_price_end_date = null,
    )
    {
    }

    public static function rules(?ValidationContext $context): array
    {
        return [
            'media_gallery.*' => ['required', 'string'],
        ];
    }

    public function toApiPayload(): array
    {
        $body = [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'url' => $this->url,
            'main_image' => $this->main_image,
            'category' => $this->category,
            'brand' => $this->brand,
            'acquisition_price' => $this->acquisition_price,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'availability' => $this->availability,
            'stock' => $this->stock,
            'media_gallery' => array_values($this->media_gallery),
            'created_at' => $this->created_at,
        ];

        if ($this->extra_attributes !== null) {
            $extra = array_filter(
                $this->extra_attributes->toArray(),
                static fn(mixed $v): bool => $v !== null && $v !== ''
            );
            if ($extra !== []) {
                $body['extra_attributes'] = $extra;
            }
        }

        if ($this->sale_price_start_date !== null && trim($this->sale_price_start_date) !== '') {
            $body['sale_price_start_date'] = $this->sale_price_start_date;
        }

        if ($this->sale_price_end_date !== null && trim($this->sale_price_end_date) !== '') {
            $body['sale_price_end_date'] = $this->sale_price_end_date;
        }

        return $body;
    }
}
