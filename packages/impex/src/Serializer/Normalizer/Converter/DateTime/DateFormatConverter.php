<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\DateTime;

use Dustin\ImpEx\Serializer\Normalizer\Converter\AttributeValueConverter;

/**
 * Converts a string date format into another format.
 */
class DateFormatConverter extends AttributeValueConverter
{
    /**
     * @var DateParser
     */
    private $parser;

    /**
     * @var string
     */
    private $format;

    public function __construct(
        DateParser $parser,
        string $format
    ) {
        $this->parser = $parser;
        $this->format = $format;
    }

    /**
     * @param string|null $value
     *
     * @return string|null
     */
    public function convert($value)
    {
        if ($value === null) {
            return null;
        }

        $dateTime = $this->parser->createDateTime((string) $value);

        if ($dateTime === null) {
            return null;
        }

        return $dateTime->format($this->format);
    }
}
