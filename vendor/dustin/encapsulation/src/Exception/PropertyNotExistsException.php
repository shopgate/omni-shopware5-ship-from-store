<?php

namespace Dustin\Encapsulation\Exception;

use Dustin\Encapsulation\EncapsulationInterface;

class PropertyNotExistsException extends EncapsulationException
{
    private $property;

    public function __construct(EncapsulationInterface $encapsulation, string $property)
    {
        $this->property = $property;

        parent::__construct(
            $encapsulation,
            \sprintf("Property '%s' does not exist in %s", $property, \get_class($encapsulation))
        );
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}
