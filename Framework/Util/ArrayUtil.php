<?php

namespace SgateShipFromStore\Framework\Util;

use Dustin\Encapsulation\EncapsulationInterface;

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

    public static function extractFromFlat(string $prefix, array $data): array
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

    public static function extractFromNested(string $path, array $data)
    {
        $pointer = $data;

        foreach (explode('.', $path) as $field) {
            if (is_object($pointer) && $pointer instanceof EncapsulationInterface) {
                $pointer = $pointer->get($field);

                continue;
            }

            if (is_numeric($field)) {
                $field = (int) $field;
            }

            $pointer = (array) $pointer;
            $pointer = $pointer[$field] ?? null;
        }

        return $pointer;
    }

    private static function isNestedKey(string $key): bool
    {
        $key = trim($key, '.');

        return strpos($key, '.') !== false;
    }
}
