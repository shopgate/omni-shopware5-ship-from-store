<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\ArrayList;

use Dustin\ImpEx\Serializer\Normalizer\Converter\AttributeValueConverter;

/**
 * Converts a value into an array and optionally keeps null-values.
 */
class ArrayConverter extends AttributeValueConverter
{
    /**
     * @var bool
     */
    private $keepNull = false;

    public function __construct(bool $keepNull = false)
    {
        $this->keepNull = $keepNull;
    }

    public function convert($value)
    {
        if ($this->keepNull && $value === null) {
            return null;
        }

        return (array) $value;
    }
}
