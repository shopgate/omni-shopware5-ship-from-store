<?php

namespace Dustin\ImpEx\Resource\Exception;

class ResourceAlreadyExistsException extends \Exception
{
    public function __construct(string $name, string $newClass, string $oldClass)
    {
        parent::__construct(
            sprintf(
                "A resource or factory with name '%s' already exists! Tried to add %s but %s was found.",
                $name, $newClass, $oldClass
            )
        );
    }
}
