<?php

namespace Dustin\ImpEx\Factory\Exception;

class MultipleFactoryException extends \Exception
{
    public function __construct(string $resource, string $factoryClass, string $existingFactoryClass)
    {
        parent::__construct(
            sprintf(
                "Multiple factories for resource '%s'! Tried to add %s but %s is already registered!",
                $sequence, $factoryClass, $existingFactoryClass
            )
        );
    }
}
