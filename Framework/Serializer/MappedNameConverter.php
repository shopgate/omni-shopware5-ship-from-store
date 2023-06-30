<?php

namespace SgateShipFromStore\Framework\Serializer;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class MappedNameConverter implements NameConverterInterface
{
    /**
     * @var array
     */
    private $mapping = [];

    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function normalize($propertyName)
    {
        return $this->mapping[$propertyName] ?? $propertyName;
    }

    public function denormalize($propertyName)
    {
        $mapping = array_flip($this->mapping);

        return $mapping[$propertyName] ?? $propertyName;
    }
}
