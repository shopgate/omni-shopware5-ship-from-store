<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter;

use Dustin\ImpEx\Encapsulation\Encapsulated;

/**
 * Used if a value can only be converted in one direction.
 * Calls the same convert function for normalizing and denormalizing.
 */
abstract class AttributeValueConverter extends AttributeConverter implements ValueConverterInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    abstract public function convert($value);

    public function denormalize($value, Encapsulated $object, string $attributeName, array $normalizedData)
    {
        return $this->convert($value);
    }

    public function normalize($value, Encapsulated $object, string $attributeName)
    {
        return $this->convert($value);
    }
}
