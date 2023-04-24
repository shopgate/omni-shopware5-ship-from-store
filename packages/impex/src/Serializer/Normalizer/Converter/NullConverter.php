<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter;

class NullConverter extends AttributeValueConverter
{
    public function convert($value)
    {
        if ((is_scalar($value) || is_array($value)) && empty($value)) {
            return null;
        }

        return $value;
    }
}
