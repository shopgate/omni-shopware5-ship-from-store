<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\String;

use Dustin\ImpEx\Serializer\Normalizer\Converter\AttributeValueConverter;

/**
 * Creates a substring with @see \substr().
 */
class Substring extends AttributeValueConverter
{
    private int $offset = 0;

    /**
     * @var int|null
     */
    private $length = null;

    public function __construct(
        int $offset,
        int $length = null
    ) {
        $this->offset = $offset;
        $this->length = $length;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function convert($value)
    {
        return substr((string) $value, $this->offset, $this->length);
    }
}
