<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\Numeric;

/**
 * Multiplies a value with a given factor.
 */
class Multiplier extends NumericConverter
{
    /**
     * @var float
     */
    private $factor;

    public function __construct(
        float $factor
    ) {
        $this->factor = $factor;
    }

    public function convertNumber(float $value): float
    {
        return $value * $this->factor;
    }
}
