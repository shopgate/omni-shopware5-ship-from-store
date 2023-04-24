<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\DateTime;

/**
 * Creates a @see \DateTimeInterface via @see \date_create_from_format().
 */
class FormattedDateParser extends DateParser
{
    private string $dateFormat;

    public function __construct(string $dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    public function createDateTime(string $value): ?\DateTimeInterface
    {
        if (empty(trim($value))) {
            return null;
        }

        $date = \date_create_from_format($this->dateFormat, (string) $value);

        return $date !== false ? $date : null;
    }
}
