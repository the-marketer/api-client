<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Common;

final class StringUtil
{
    /**
     * Trims string values only; other types are returned unchanged.
     */
    public static function trim(mixed $value): mixed
    {
        return is_string($value) ? trim($value) : $value;
    }
}
