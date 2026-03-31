<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Common;

/**
 * Normalizări pentru array-uri de input înainte de mapare la DTO-uri (trim, coercare numerică).
 */
final class PayloadArrayNormalizer
{
    private function __construct()
    {
    }

    /**
     * Elimină spațiile de la capetele valorilor string pentru cheile date.
     *
     * @param array<string, mixed> $data
     * @param list<string>         $keys
     *
     * @return array<string, mixed>
     */
    public static function trimStringFields(array $data, array $keys): array
    {
        foreach ($keys as $key) {
            if (isset($data[$key]) && is_string($data[$key])) {
                $data[$key] = trim($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Convertește stringuri numerice la int/float (ex. din JSON sau form).
     *
     * @param array<string, mixed> $data
     * @param list<string>         $intKeys
     * @param list<string>         $floatKeys
     *
     * @return array<string, mixed>
     */
    public static function coerceNumericStrings(array $data, array $intKeys, array $floatKeys): array
    {
        foreach ($intKeys as $key) {
            if (isset($data[$key]) && is_string($data[$key]) && is_numeric($data[$key])) {
                $data[$key] = (int) $data[$key];
            }
        }
        foreach ($floatKeys as $key) {
            if (isset($data[$key]) && is_string($data[$key]) && is_numeric($data[$key])) {
                $data[$key] = (float) $data[$key];
            }
        }

        return $data;
    }
}
