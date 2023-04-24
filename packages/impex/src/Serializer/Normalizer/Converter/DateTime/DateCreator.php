<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\DateTime;

/**
 * Creates a @see \DateTimeInterface via @see \date_create().
 */
class DateCreator extends DateParser
{
    public function createDateTime(string $value): ?\DateTimeInterface
    {
        $date = \date_create($value);

        return $date !== false ? $date : null;
    }
}
