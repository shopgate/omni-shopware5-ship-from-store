<?php

namespace SgateShipFromStore\Components\Order;

use SgateShipFromStore\Components\Order\Encapsulation\Order;

interface OrderExtractionInterface
{
    public function getOrder(): Order;
}
