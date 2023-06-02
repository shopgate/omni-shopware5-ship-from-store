<?php

namespace SgateShipFromStore\Framework\Reader;

use Dustin\Encapsulation\EncapsulationInterface;
use Dustin\ImpEx\Serializer\Normalizer\EncapsulationNormalizer;
use SgateShipFromStore\Framework\Exception\RecordNotFoundException;

abstract class Reader implements ReaderInterface
{
    protected EncapsulationNormalizer $denormalizer;

    public function __construct(
        EncapsulationNormalizer $denormalizer
    ) {
        if ($denormalizer->getEncapsulationClass() === null) {
            throw new \RuntimeException('Denormalizer must return encapsulation class name');
        }

        $this->denormalizer = $denormalizer;
    }

    abstract protected function read($identifier): ?array;

    public function get(iterable $identifiers): \Generator
    {
        foreach ($identifiers as $identifier) {
            $record = $this->read($identifier);

            if ($record === null) {
                throw new RecordNotFoundException($identifier);
            }

            yield $this->denormalize($record);
        }
    }

    protected function denormalize(array $data): EncapsulationInterface
    {
        return $this->denormalizer->denormalize(
            $data,
            $this->denormalizer->getEncapsulationClass(),
            null,
            [EncapsulationNormalizer::GROUPS => ['denormalization']]
        );
    }
}
