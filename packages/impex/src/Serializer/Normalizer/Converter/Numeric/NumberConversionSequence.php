<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\Numeric;

/**
 * A sequence of number conversion. Used for example parsing, multipling and formatting a value.
 */
class NumberConversionSequence extends NumericConverter
{
    /**
     * @var array
     */
    private $converters = [];

    public function __construct(
        NumericConverter ...$converters
    ) {
        $this->converters = $converters;
    }

    public function convertNumber(float $value): float
    {
        foreach ($this->converters as $converter) {
            $value = $converter->convertNumber($value);
        }

        return $value;
    }
}
