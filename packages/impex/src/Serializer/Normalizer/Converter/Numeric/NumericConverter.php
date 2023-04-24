<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\Numeric;

use Dustin\ImpEx\Serializer\Normalizer\Converter\AttributeValueConverter;

/**
 * Used to specifically convert numeric values.
 */
abstract class NumericConverter extends AttributeValueConverter
{
    abstract public function convertNumber(float $value): float;

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
