<?php

namespace Dustin\ImpEx\Serializer\Normalizer\Converter;

use Dustin\ImpEx\Encapsulation\Encapsulated;

/**
 * Returns the root of all data. Or optionally a pointed value started from root.
 */
class RootPointer extends AttributeConverter
{
    private ?string $field;

    private ?Pointer $innerPointer = null;

    public function __construct(
        string $field = null
    ) {
        $this->field = $field;

        if ($field !== null) {
            $this->innerPointer = new Pointer($field);
        }
    }

    public function denormalize($value, Encapsulated $object, string $attributeName, array $normalizedData)
    {
        if ($this->field === null) {
            return $normalizedData;
        }

        return $this->innerPointer->denormalize($normalizedData, $object, $attributeName, $normalizedData);
    }

    /**
     * @return Encapsulated
     */
    public function normalize($value, Encapsulated $object, string $attributeName)
    {
        throw new \RuntimeException(sprintf('%s does not support normalization.', self::class));
    }
}
