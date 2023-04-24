<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\Numeric;

use Dustin\ImpEx\Serializer\Normalizer\Converter\AttributeValueConverter;

/**
 * Formats a number to string via @see \number_format().
 */
class Formatter extends AttributeValueConverter
{
    /**
     * @var string
     */
    private $decimalSeparator;

    /**
     * @var string
     */
    private $thousandsSeparator;

    /**
     * @var int
     */
    private $decimals;

    public function __construct(
        string $decimalSeparator = '.',
        string $thousandsSeparator = ',',
        int $decimals = 3
    ) {
        $this->decimalSeparator = $decimalSeparator;
        $this->thousandsSeparator = $thousandsSeparator;
        $this->decimals = $decimals;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function convert($value)
    {
        return $this->formatNumber((float) $value);
    }

    public function formatNumber(float $number): string
    {
        return \number_format(
            $number,
            $this->decimals,
            $this->decimalSeparator,
            $this->thousandsSeparator
        );
    }
}
