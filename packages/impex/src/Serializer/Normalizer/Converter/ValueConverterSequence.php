<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter;

/**
 * Calls a sequence of @see ValueConverterInterface for a value.
 */
class ValueConverterSequence extends AttributeValueConverter
{
    /**
     * @var array
     */
    private $converters = [];

    public function __construct(
        ValueConverterInterface ...$converters
    ) {
        $this->converters = $converters;
    }

    public function convert($value)
    {
        foreach ($this->converters as $converter) {
            $value = $converter->convert($value);
        }

        return $value;
    }
}
