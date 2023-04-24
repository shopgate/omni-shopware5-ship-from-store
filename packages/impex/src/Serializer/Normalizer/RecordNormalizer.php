<?php

namespace Dustin\ImpEx\Serializer\Normalizer;

use Dustin\ImpEx\Encapsulation\Raw;
use Dustin\ImpEx\Encapsulation\Record;
use Dustin\ImpEx\Serializer\Normalizer\Converter\EncapsulationConverter;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class RecordNormalizer extends EncapsulationNormalizer
{
    public function __construct(
        NameConverterInterface $nameConverter = null,
        array $defaultContext = []
    ) {
        $callbacks = [];

        foreach (Record::getDefaultProperties() as $property => $class) {
            $callbacks[$property] = new EncapsulationConverter($class);
        }

        if (isset($defaultContext[self::CALLBACKS]) && is_array($defaultContext[self::CALLBACKS])) {
            $callbacks = array_merge($callbacks, $defaultContext[self::CALLBACKS]);
        }

        $defaultContext[self::CALLBACKS] = $callbacks;

        parent::__construct($nameConverter, $defaultContext);
    }

    public function getEncapsulationClass(): ?string
    {
        return Record::class;
    }

    /**
     * @return Record
     */
    protected function instantiateObject(array &$data, $class, array &$context, \ReflectionClass $reflectionClass, $allowedAttributes, string $format = null)
    {
        $record = parent::instantiateObject($data, $class, $context, $reflectionClass, $allowedAttributes, $format);
        $record->set('raw', new Raw($data));

        return $record;
    }

    protected function useAttributeCache(): bool
    {
        return true;
    }
}
