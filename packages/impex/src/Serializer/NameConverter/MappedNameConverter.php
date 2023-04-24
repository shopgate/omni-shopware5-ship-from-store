<?php

namespace Dustin\ImpEx\Serializer\NameConverter;

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

    /**
     * @param string $propertyName
     *
     * @return string
     */
    public function normalize($propertyName)
    {
        if (isset($this->mapping[$propertyName])) {
            return $this->mapping[$propertyName];
        }

        return $propertyName;
    }

    /**
     * @param string $propertyName
     *
     * @return string
     */
    public function denormalize($propertyName)
    {
        $mapping = array_flip($this->mapping);

        if (isset($mapping[$propertyName])) {
            return $mapping[$propertyName];
        }

        return $propertyName;
    }
}
