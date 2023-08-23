<?php

namespace SgateShipFromStore\Components\Customer\Subscriber;

use SgateShipFromStore\Framework\Sequence\Subscriber\AbstractRecordHandlingTaskSubscriber;

class CustomerEmailExportSubscriber extends AbstractRecordHandlingTaskSubscriber
{
    public static function getEventName(): string
    {
        return 'Shopware_CronJob_SgateCustomerEmailExport';
    }

    public function getSequence(): string
    {
        return 'sgate_customer_email_export';
    }
}
