<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter;

use Dustin\ImpEx\Encapsulation\AbstractEncapsulation;
use Dustin\ImpEx\Encapsulation\Encapsulated;
use Dustin\ImpEx\Encapsulation\Encapsulation;

/**
 * Converts an array into an @see AbstractEncapsulation.
 */
class EncapsulationConverter extends AttributeConverter
{
    /**
     * @var string
     */
    protected $encapsulationClass;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $encapsulationClass = Encapsulation::class
    ) {
        if (!is_subclass_of($encapsulationClass, AbstractEncapsulation::class)) {
            throw new \InvalidArgumentException(sprintf('Class %s does not inherit from %s', $encapsulationClass, AbstractEncapsulation::class));
        }

        $this->encapsulationClass = $encapsulationClass;
    }

    /**
     * @param array|null $data
     *
     * @return AbstractEncapsulation|null
     */
    public function denormalize($data, Encapsulated $object, string $attributeName, array $normalizedData)
    {
        if ($data === null) {
            return null;
        }

        return new $this->encapsulationClass((array) $data);
    }

    /**
     * @param Encapsulated|null $value
     *
     * @return array|null
     *
     * @throws \InvalidArgumentException
     */
    public function normalize($value, Encapsulated $object, string $attributeName)
    {
        if ($value === null) {
            return null;
        }

        if (!is_object($value) || !($value instanceof Encapsulated)) {
            throw new \InvalidArgumentException(sprintf('Converting other values than %s objects is not supported!', $this->encapsulationClass));
        }

        return $value->normalize();
    }
}
