<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\Numeric;

/**
 * Rounds a numeric value with @see \round().
 */
class Rounder extends NumericConverter
{
    /**
     * @var int
     */
    private $precision;

    /**
     * @var int
     */
    private $mode;

    public function __construct(
        int $precision = 0,
        int $mode = PHP_ROUND_HALF_UP
    ) {
        $this->precision = $precision;
        $this->mode = $mode;
    }

    public function convertNumber(float $value): float
    {
        return \round($value, $this->precision, $mode);
    }
}
