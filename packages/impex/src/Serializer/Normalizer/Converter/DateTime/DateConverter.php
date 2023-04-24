<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter\DateTime;

use Dustin\ImpEx\Encapsulation\Encapsulated;
use Dustin\ImpEx\Serializer\Normalizer\Converter\AttributeConverter;

/**
 * Converts a string into @see \DateTimeInterface and vice versa.
 */
class DateConverter extends AttributeConverter
{
    /**
     * @var DateParser
     */
    private $dateParser;

    /**
     * @var string
     */
    private $dateFormat;

    public function __construct(DateParser $dateParser, string $dateFormat)
    {
        $this->dateParser = $dateParser;
        $this->dateFormat = $dateFormat;
    }

    /**
     * @param string|null $value
     *
     * @return \DateTimeInterface|null
     */
    public function denormalize($value, Encapsulated $object, string $attributeName, array $normalizedData)
    {
        if ($value === null) {
            return null;
        }

        return $this->dateParser->createDateTime($value);
    }

    /**
     * @param \DateTimeInterface|null $value
     *
     * @return string|null
     *
     * @throws \InvalidArgumentException
     */
    public function normalize($value, Encapsulated $object, string $attributeName)
    {
        if ($value === null) {
            return null;
        }

        if (!is_object($value) || !($value instanceof \DateTimeInterface)) {
            throw new \InvalidArgumentException(\sprintf('Expected date time to be %s for normalization. %s given.', \DateTimeInterface::class, gettype($value)));
        }

        return $value->format($this->dateFormat);
    }
}
