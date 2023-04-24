<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\Numeric;

/**
 * Adds a value to a number.
 */
class Adder extends NumericConverter
{
    private float $add;

    public function __construct(float $add)
    {
        $this->add = $add;
    }

    public function convertNumber(float $value): float
    {
        return $value + $this->add;
    }
}
