<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Products;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;
use TheMarketer\ApiClient\Common\AbstractPayload;

class UpdateProduct extends AbstractPayload
{
    public function __construct(
        #[Required]
        #[Rule('integer')]
        public int $id,
        #[Required]
        #[Rule('string', 'filled')]
        public string $sku,
        #[Sometimes]
        #[Rule('nullable', 'string')]
        public ?string $name = null,
        #[Sometimes]
        #[Rule('nullable', 'string')]
        public ?string $description = null,
        #[Sometimes]
        #[Rule('nullable', 'string')]
        public ?string $url = null,
        #[Sometimes]
        #[Rule('nullable', 'string')]
        public ?string $main_image = null,
        #[Sometimes]
        #[Rule('nullable', 'string')]
        public ?string $category = null,
        #[Sometimes]
        #[Rule('nullable', 'string')]
        public ?string $brand = null,
        #[Sometimes]
        #[Rule('nullable', 'numeric')]
        public ?float $acquisition_price = null,
        #[Sometimes]
        #[Rule('nullable', 'numeric')]
        public ?float $price = null,
        #[Sometimes]
        #[Rule('nullable', 'numeric')]
        public ?float $sale_price = null,
        #[Sometimes]
        #[Rule('nullable', 'integer')]
        public ?int $availability = null,
        #[Sometimes]
        #[Rule('nullable', 'integer')]
        public ?int $stock = null,
        #[Sometimes]
        #[MapInputName('media_gallery.0')]
        #[Rule('nullable', 'string')]
        public ?string $media_gallery0 = null,
        #[Sometimes]
        #[MapInputName('media_gallery.1')]
        #[Rule('nullable', 'string')]
        public ?string $media_gallery1 = null,
        #[Sometimes]
        #[Rule('nullable', 'string')]
        public ?string $created_at = null,
        #[Sometimes]
        public ?ProductExtraAttributes $extra_attributes = null,
        #[Sometimes]
        #[Rule('nullable', 'string')]
        public ?string $sale_price_start_date = null,
        #[Sometimes]
        #[Rule('nullable', 'string')]
        public ?string $sale_price_end_date = null,
    ) {
    }
    
    public function toApiPayload(): array
    {
        $body = [
            'id' => $this->id,
            'sku' => $this->sku,
        ];

        if ($this->name !== null && trim($this->name) !== '') {
            $body['name'] = $this->name;
        }

        if ($this->description !== null && trim($this->description) !== '') {
            $body['description'] = $this->description;
        }

        if ($this->url !== null && trim($this->url) !== '') {
            $body['url'] = $this->url;
        }

        if ($this->main_image !== null && trim($this->main_image) !== '') {
            $body['main_image'] = $this->main_image;
        }

        if ($this->category !== null && trim($this->category) !== '') {
            $body['category'] = $this->category;
        }

        if ($this->brand !== null && trim($this->brand) !== '') {
            $body['brand'] = $this->brand;
        }

        if ($this->acquisition_price !== null) {
            $body['acquisition_price'] = $this->acquisition_price;
        }

        if ($this->price !== null) {
            $body['price'] = $this->price;
        }

        if ($this->sale_price !== null) {
            $body['sale_price'] = $this->sale_price;
        }

        if ($this->availability !== null) {
            $body['availability'] = $this->availability;
        }

        if ($this->stock !== null) {
            $body['stock'] = $this->stock;
        }

        $gallery = [];
        foreach ([0 => $this->media_gallery0, 1 => $this->media_gallery1] as $i => $v) {
            if ($v !== null && trim($v) !== '') {
                $gallery[$i] = $v;
            }
        }
        if ($gallery !== []) {
            ksort($gallery);
            $body['media_gallery'] = $gallery;
        }

        if ($this->created_at !== null && trim($this->created_at) !== '') {
            $body['created_at'] = $this->created_at;
        }

        if ($this->extra_attributes !== null) {
            $extra = static::filterNonEmpty($this->extra_attributes->toArray());
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
