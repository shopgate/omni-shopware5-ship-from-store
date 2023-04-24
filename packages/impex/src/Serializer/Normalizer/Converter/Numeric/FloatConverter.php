<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\Numeric;

/**
 * Used to cast any value to float without special conversion.
 */
class FloatConverter extends NumericConverter
{
    public function convertNumber(float $value): float
    {
        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return float
     */
    public function convert($value)
    {
        return $this->convertNumber((float) $value);
    }
}
