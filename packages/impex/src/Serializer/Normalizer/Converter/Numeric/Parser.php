<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\Numeric;

use Dustin\ImpEx\Serializer\Normalizer\Converter\AttributeValueConverter;

/**
 * Parses a string into float with removing unwanted characters.
 */
class Parser extends AttributeValueConverter
{
    /**
     * @var string
     */
    private $decimalSeparator;

    /**
     * @var string
     */
    private $thousandsSeparator;

    public function __construct(
        string $decimalSeparator = '.',
        string $thousandsSeparator = ','
    ) {
        $this->decimalSeparator = $decimalSeparator;
        $this->thousandsSeparator = $thousandsSeparator;
    }

    /**
     * @param string $value
     *
     * @return float
     */
    public function convert($value)
    {
        return $this->parseNumber((string) $value);
    }

    public function parseNumber(string $value): float
    {
        if (!empty($this->thousandsSeparator)) {
            $value = \str_replace($this->thousandsSeparator, '', $value);
        }

        $value = \str_replace($this->decimalSeparator, '.', $value);

        $value = preg_replace('/[^0-9.]/', '', $value);

        return floatval($value);
    }
}
