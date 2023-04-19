<?php

namespace Dustin\Encapsulation\Exception;

class ImmutableException extends \Exception
{
    public function __construct($encapsulationOrContainer)
    {
        parent::__construct(
            sprintf('Object of class %s is immutable.', get_class($encapsulationOrContainer))
        );
    }
}
