<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter;

use Dustin\ImpEx\Encapsulation\Encapsulated;

/**
 * A sequence of converters called for a value.
 */
class AttributeConverterSequence extends AttributeConverter
{
    /**
     * @var array
     */
    private $converters = [];

    public function __construct(AttributeConverter ...$converters)
    {
        $this->converters = $converters;
    }

    public function denormalize($value, Encapsulated $object, string $attributeName, array $normalizedData)
    {
        foreach ($this->converters as $converter) {
            $value = $converter->denormalize($value, $object, $attributeName, $normalizedData);
        }

        return $value;
    }

    public function normalize($value, Encapsulated $object, string $attributeName)
    {
        foreach ($this->converters as $converter) {
            $value = $converter->normalize($value, $object, $attributeName);
        }

        return $value;
    }
}
