<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\String;

use Dustin\ImpEx\Serializer\Normalizer\Converter\AttributeValueConverter;

/**
 * Trims a string.
 */
class Trimmer extends AttributeValueConverter
{
    public const LEFT = 'left';

    public const RIGHT = 'right';

    public const BOTH = 'both';

    private $characters = null;

    private $mode = self::BOTH;

    /**
     * @throws \Exception
     */
    public function __construct(
        string $characters = null,
        string $mode = self::BOTH
    ) {
        $this->characters = $characters;

        if (!in_array($mode, [self::LEFT, self::RIGHT, self::BOTH])) {
            throw new \Exception(sprintf('%s is not a valid trim mode.', $mode));
        }

        $this->mode = $mode;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function convert($value)
    {
        $value = (string) $value;

        switch ($this->mode) {
            case self::LEFT:
                return $this->characters ? ltrim($value, $this->characters) : ltrim($value);
            case self::RIGHT:
                return $this->characters ? rtrim($value, $this->characters) : rtrim($value);
            case self::BOTH:
                return $this->characters ? trim($value, $this->characters) : trim($value);
        }
    }
}
