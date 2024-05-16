<?php

namespace SgateShipFromStore\Framework\Sequence;

use Dustin\ImpEx\Sequence\Transferor;

class ArrayTransferor implements Transferor
{
    private $records = [];

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function passRecords(): \Generator
    {
        yield from $this->records;
    }
}
