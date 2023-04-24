<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\ArrayList;

use Dustin\ImpEx\Encapsulation\Encapsulated;
use Dustin\ImpEx\Serializer\Normalizer\Converter\AttributeConverter;

/**
 * A mapping of converters used for arrays.
 */
class ConverterMapping extends AttributeConverter
{
    private array $converters = [];

    public function __construct(array $converters)
    {
        foreach ($converters as $name => $converter) {
            $this->setConverter($name, $converter);
        }
    }

    public function setConverter(string $field, ?AttributeConverter $converter)
    {
        $this->converters[$field] = $converter;
    }

    public function getConverter(string $field): ?AttributeConverter
    {
        return isset($this->converters[$field]) ? $this->converters[$field] : null;
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    public function denormalize($fields, Encapsulated $object, string $attributeName, array $normalizedData)
    {
        $data = [];

        foreach ((array) $fields as $name => $value) {
            $converter = $this->getConverter($name);
            $data[$name] = $converter !== null ? $converter->denormalize($value, $object, $name, $normalizedData) : $value;
        }

        return $data;
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    public function normalize($fields, Encapsulated $object, string $attributeName)
    {
        $data = [];

        foreach ((array) $fields as $name => $value) {
            $converter = $this->getConverter($name);
            $data[$name] = $converter !== null ? $converter->normalize($value, $object, $name) : $value;
        }

        return $data;
    }
}
