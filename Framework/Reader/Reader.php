<?php

namespace SgateShipFromStore\Framework\Reader;

use Doctrine\DBAL\Connection;
use Dustin\ImpEx\Serializer\Normalizer\EncapsulationNormalizer;

abstract class Reader implements ReaderInterface
{
    protected Connection $connection;

    protected EncapsulationNormalizer $denormalizer;

    protected string $type;

    public function __construct(
        Connection $connection,
        EncapsulationNormalizer $denormalizer
    ) {
        if ($denormalizer->getEncapsulationClass() === null) {
            throw new \RuntimeException('Denormalizer must return encapsulation class name');
        }

        $this->connection = $connection;
        $this->denormalizer = $denormalizer;
    }

    abstract protected function read($identifier): array;

    public function get(iterable $identifiers): \Generator
    {
        foreach ($identifiers as $identifier) {
            yield $this->denormalizer->denormalize(
                $this->read((string) $identifier),
                $this->denormalizer->getEncapsulationClass(),
                null,
                [EncapsulationNormalizer::GROUPS => ['denormalization']]
            );
        }
    }
}
