<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\DateTime;

use Dustin\ImpEx\Serializer\Normalizer\Converter\AttributeValueConverter;

/**
 * Converts a string into @see \DateTimeInterface.
 */
abstract class DateParser extends AttributeValueConverter
{
    abstract public function createDateTime(string $value): ?\DateTimeInterface;

    /**
     * @param string|null $value
     *
     * @return \DateTimeInterface|null
     */
    public function convert($value)
    {
        if ($value === null) {
            return null;
        }

        return $this->createDateTime((string) $value);
    }
}
