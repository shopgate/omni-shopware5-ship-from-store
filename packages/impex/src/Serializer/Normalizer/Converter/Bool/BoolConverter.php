<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\Bool;

use Dustin\ImpEx\Serializer\Normalizer\Converter\AttributeValueConverter;

/**
 * Converts value to boolean and optionally keeps null-values.
 */
class BoolConverter extends AttributeValueConverter
{
    protected bool $acceptNull = false;

    public function __construct(bool $acceptNull = false)
    {
        $this->acceptNull = $acceptNull;
    }

    /**
     * {@inheritdoc}
     */
    public function convert($value)
    {
        if ($this->acceptNull && $value === null) {
            return null;
        }

        return boolval($value);
    }
}
