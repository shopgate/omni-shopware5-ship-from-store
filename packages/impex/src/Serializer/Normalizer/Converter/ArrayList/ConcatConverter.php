<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\ArrayList;

use Dustin\ImpEx\Encapsulation\Encapsulated;
use Dustin\ImpEx\Serializer\Normalizer\Converter\AttributeConverter;

/**
 * Converts a string to array and vice versa via @see \implode() and @see \explode().
 */
class ConcatConverter extends AttributeConverter
{
    /**
     * @var string
     */
    private $separator;

    /**
     * @var bool
     */
    private $reverse;

    public function __construct(string $separator, bool $reverse = false)
    {
        $this->separator = $separator;
        $this->reverse = $reverse;
    }

    /**
     * @param string|array|null value
     *
     * @return string|array|null
     */
    public function denormalize($value, Encapsulated $object, string $attributeName, array $normalizedData)
    {
        if ($value === null) {
            return null;
        }

        if ($this->reverse) {
            return $this->implode($this->separator, (array) $value);
        }

        return explode($this->separator, (string) $value);
    }

    /**
     * @param string|array|null $value
     *
     * @return string|array|null
     */
    public function normalize($value, Encapsulated $object, string $attributeName)
    {
        if ($value === null) {
            return null;
        }

        if ($this->reverse) {
            return explode($this->separator, (string) $value);
        }

        return implode($this->separator, (array) $value);
    }
}
