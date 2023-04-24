<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter;

use Dustin\ImpEx\Encapsulation\Encapsulated;
use Symfony\Component\Serializer\Serializer;

/**
 * Serializes a value.
 */
class SerializerConverter extends AttributeConverter
{
    public const NORMALIZE = 0;

    public const SERIALIZE = 1;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $mode;

    /**
     * @var string
     */
    private $format;

    public function __construct(
        Serializer $serializer,
        string $type,
        int $mode = self::NORMALIZE,
        string $format = null
    ) {
        $this->serializer = $serializer;
        $this->type = $type;
        $this->mode = $mode;
        $this->format = $format;
    }

    public function denormalize($value, Encapsulated $object, string $attributeName, array $normalizedData)
    {
        if ($value === null) {
            return null;
        }

        if ($this->mode === self::NORMALIZE) {
            return $this->serializer->denormalize($value, $this->type);
        }

        return $this->serializer->deserialize($value, $this->type, $this->format);
    }

    public function normalize($value, Encapsulated $object, string $attributeName)
    {
        if ($value === null) {
            return null;
        }

        if ($this->mode === self::NORMALIZE) {
            return $this->serializer->normalize((array) $value);
        }

        return $this->serializer->serialize($value, $this->format);
    }
}
