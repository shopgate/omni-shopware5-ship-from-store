<?php

namespace Dustin\Encapsulation\Exception;

use Dustin\Encapsulation\EncapsulationInterface;

class StaticException extends EncapsulationException
{
    public function __construct(EncapsulationInterface $encapsulation, string $property)
    {
        parent::__construct(
            $encapsulation,
            \sprintf("'%s' is static and cannot be handled by encapsulations.", $property)
        );
    }
}
