<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\String;

use Dustin\ImpEx\Encapsulation\Encapsulated;
use Dustin\ImpEx\Serializer\Normalizer\Converter\AttributeConverter;

/**
 * Encodes or decodes a string with @see \htmlentities() and @see \html_entity_decode().
 */
class HtmlEntityEncoder extends AttributeConverter
{
    private bool $reverse;

    public function __construct(bool $reverse = false)
    {
        $this->reverse = $reverse;
    }

    /**
     * @param string $value
     *
     * @return mixed
     */
    public function denormalize($value, Encapsulated $object, string $attributeName, array $normalizedData)
    {
        if ($this->reverse) {
            return htmlentities((string) $value);
        }

        return html_entity_decode((string) $value);
    }

    /**
     * @param string $value
     *
     * @return mixed
     */
    public function normalize($value, Encapsulated $object, string $attributeName)
    {
        if ($this->reverse) {
            return html_entity_decode((string) $value);
        }

        return htmlentities((string) $value);
    }
}
