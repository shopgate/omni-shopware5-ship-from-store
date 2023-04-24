<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter;

use Dustin\ImpEx\Encapsulation\Encapsulated;

/**
 * Used to return a value in an array or @see Encapsulated.
 * Example:
 * [
 *  "foo" => [
 *      "bar" => [
 *          "test" => "Hello world!"
 *       ]
 *  ]
 *]
 * Use the string pointer 'foo.bar.test' to get the value 'Hello world!'.
 * It is also possible to use numeric indexes.
 * Converts a pointer string to a nested array in the other direction.
 */
class Pointer extends AttributeConverter
{
    private string $field;

    private bool $reverse;

    public function __construct(
        string $field,
        bool $reverse = false)
    {
        $this->field = $field;
        $this->reverse = $reverse;
    }

    public function denormalize($value, Encapsulated $object, string $attributeName, array $normalizedData)
    {
        if ($this->reverse) {
            return $this->convertToArray($value);
        }

        return $this->fetchValue($value);
    }

    public function normalize($value, Encapsulated $object, string $attributeName)
    {
        if ($this->reverse) {
            return $this->fetchValue($value);
        }

        return $this->convertToArray($value);
    }

    /**
     * @param mixed $value
     */
    protected function convertToArray($value): array
    {
        $data = [];
        $current = &$data;

        foreach (explode('.', $this->field) as $field) {
            if (is_numeric($field)) {
                $field = (int) $field;
            }

            $current[$field] = [];
            $current = &$current[$field];
        }

        $current = $value;

        return $data;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function fetchValue($value)
    {
        $pointer = $value;

        foreach (explode('.', $this->field) as $field) {
            if (is_object($pointer) && $pointer instanceof Encapsulated) {
                $pointer = $pointer->get($field);

                continue;
            }

            if (is_numeric($field)) {
                $field = (int) $field;
            }

            $pointer = (array) $pointer;
            $pointer = isset($pointer[$field]) ? $pointer[$field] : null;
        }

        return $pointer;
    }
}
