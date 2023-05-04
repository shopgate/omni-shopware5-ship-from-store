<?php

namespace SgateShipFromStore\Framework\Util;

class ArrayUtil
{
    public static function flatToNested(array $data): array
    {
        $nested = [];

        foreach ($data as $key => $value) {
            if (!static::isNestedKey((string) $key)) {
                $nested[$key] = $value;

                continue;
            }

            $key = trim($key, '.');
            $current = &$nested;

            foreach (explode('.', $key) as $field) {
                if (is_numeric($field)) {
                    $field = (int) $field;
                }

                if (!isset($current[$field])) {
                    $current[$field] = [];
                }

                $current = &$current[$field];
            }

            $current = $value;
        }

        return $nested;
    }

    public static function extract(string $prefix, array $data): array
    {
        $prefix = trim($prefix, '.').'.';

        $extract = [];

        foreach ($data as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $extractKey = substr($key, strlen($prefix));
                $extract[$extractKey] = $value;
            }
        }

        return $extract;
    }

    private static function isNestedKey(string $key): bool
    {
        $key = trim($key, '.');

        return strpos($key, '.') !== false;
    }
}
