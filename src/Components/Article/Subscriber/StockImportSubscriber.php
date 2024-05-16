<?php

namespace SgateShipFromStore\Components\Article\Subscriber;

use SgateShipFromStore\Framework\Sequence\Subscriber\AbstractRecordHandlingTaskSubscriber;

class StockImportSubscriber extends AbstractRecordHandlingTaskSubscriber
{
    public static function getEventName(): string
    {
        return 'Shopware_CronJob_SgateStockImport';
    }

    public function getSequence(): string
    {
        return 'sgate_inventory_import';
    }
}
