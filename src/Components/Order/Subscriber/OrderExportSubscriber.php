<?php

namespace SgateShipFromStore\Components\Order\Subscriber;

use SgateShipFromStore\Framework\Sequence\Subscriber\AbstractRecordHandlingTaskSubscriber;

class OrderExportSubscriber extends AbstractRecordHandlingTaskSubscriber
{
    public static function getEventName(): string
    {
        return 'Shopware_CronJob_SgateOrderExport';
    }

    public function getSequence(): string
    {
        return 'sgate_order_export';
    }
}
