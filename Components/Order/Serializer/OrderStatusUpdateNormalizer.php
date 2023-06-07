<?php

namespace SgateShipFromStore\Components\Order\Serializer;

use SgateShipFromStore\Components\Order\Encapsulation\OrderStatusUpdate;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;

class OrderStatusUpdateNormalizer extends EncapsulationNormalizer
{
    public function getEncapsulationClass(): ?string
    {
        return OrderStatusUpdate::class;
    }
}
