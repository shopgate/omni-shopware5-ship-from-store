<?php

namespace SgateShipFromStore\Framework\Reader;

interface ReaderInterface
{
    public function getNextUpIdentifiers(): \Generator;

    public function get(iterable $identifiers): \Generator;
}
