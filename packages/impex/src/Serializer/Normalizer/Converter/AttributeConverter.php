<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter;

use Dustin\ImpEx\Encapsulation\Encapsulated;

abstract class AttributeConverter
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    abstract public function denormalize($value, Encapsulated $object, string $attributeName, array $normalizedData);

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    abstract public function normalize($value, Encapsulated $object, string $attributeName);
}
