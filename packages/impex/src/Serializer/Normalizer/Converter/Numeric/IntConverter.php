<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\Numeric;

/**
 * Used to cast a value to int.
 */
class IntConverter extends NumericConverter
{
    public function convertNumber(float $value): float
    {
        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return int
     */
    public function convert($value)
    {
        return (int) $this->convertNumber((float) $value);
    }
}
