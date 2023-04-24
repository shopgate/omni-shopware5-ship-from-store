<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\ArrayList;

use Dustin\ImpEx\Encapsulation\Encapsulated;
use Dustin\ImpEx\Serializer\Normalizer\Converter\AttributeConverter;

/**
 * Calls a converter for every element of an array.
 * Result array can optionally be filtered with optional callback.
 */
class ListConverter extends AttributeConverter
{
    /**
     * @var AttributeConverter
     */
    private $converter;

    /**
     * @var bool
     */
    private $filter = false;

    /**
     * @var callable|null
     */
    private $filterCallback = null;

    public function __construct(
        AttributeConverter $converter,
        bool $filter = false,
        callable $filterCallback = null
    ) {
        $this->converter = $converter;
        $this->filter = $filter;
        $this->filterCallback = $filterCallback;
    }

    /**
     * @param array $value
     *
     * @return array
     */
    public function denormalize($value, Encapsulated $object, string $attributeName, array $normalizedData)
    {
        $data = array_map(function ($item) use ($object, $attributeName, $normalizedData) {
            return $this->converter->denormalize($item, $object, $attributeName, $normalizedData);
        }, (array) $value);

        if ($this->filter === true) {
            //callback is only nullable in php 8
            if ($this->filterCallback !== null) {
                $data = array_filter($data, $this->filterCallback);
            } else {
                $data = array_filter($data);
            }
        }

        return $data;
    }

    /**
     * @param array $value
     *
     * @return array
     */
    public function normalize($value, Encapsulated $object, string $attributeName)
    {
        $data = array_map(function ($item) use ($object, $attributeName) {
            return $this->converter->normalize($item, $object, $attributeName);
        }, (array) $value);

        if ($this->filter === true) {
            //callback is only nullable in php 8
            if ($this->filterCallback !== null) {
                $data = array_filter($data, $this->filterCallback);
            } else {
                $data = array_filter($data);
            }
        }

        return $data;
    }
}
