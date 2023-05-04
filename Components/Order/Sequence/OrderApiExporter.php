<?php

namespace SgateShipFromStore\Components\Order\Sequence;

use Dustin\ImpEx\Sequence\RecordHandling;
use Dustin\ImpEx\Sequence\Transferor;

class OrderApiExporter implements RecordHandling
{
    public function handle(Transferor $transferor): void
    {
        foreach ($transferor->passRecords() as $order) {
            print_r($order);
            exit;
        }
    }
}
