<?php

namespace SgateShipFromStore\Components\Order\Serializer;

use SgateShipFromStore\Components\Order\Encapsulation\OrderStatusUpdate;

class OrderStatusUpdateNormalizer extends OrderRelationNormalizer
{
    public function getEncapsulationClass(): ?string
    {
        return OrderStatusUpdate::class;
    }
}
