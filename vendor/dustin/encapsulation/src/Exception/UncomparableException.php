<?php

namespace Dustin\Encapsulation\Exception;

class UncomparableException extends \Exception
{
    public function __construct($value)
    {
        $type = is_object($value) ? get_class($value) : gettype($value);

        parent::__construct(sprintf('%s is not comparable for intersection calculation', $type));
    }
}
