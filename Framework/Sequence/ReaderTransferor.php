<?php

namespace SgateShipFromStore\Framework\Sequence;

use Dustin\ImpEx\Sequence\Transferor;
use SgateShipFromStore\Framework\Exception\RecordNotFoundException;
use SgateShipFromStore\Framework\Reader\ReaderInterface;

class ReaderTransferor implements Transferor
{
    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @var iterable|null
     */
    private $identifiers = null;

    public function __construct(
        ReaderInterface $reader,
        ?iterable $identifiers = null
    ) {
        $this->reader = $reader;
        $this->identifiers = $identifiers;
    }

    public function passRecords(): \Generator
    {
        $identifiers = $this->identifiers ?? $this->reader->getNextUpIdentifiers();

        foreach ($identifiers as $identifier) {
            try {
                foreach ($this->reader->get([$identifier]) as $record) {
                    yield $record;
                }
            } catch (RecordNotFoundException $e) {
                // Ignore not found records
            }
        }
    }
}
