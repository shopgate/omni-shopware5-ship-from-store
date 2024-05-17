<?php

namespace SgateShipFromStore\Components\Order\Encapsulation;

use Dustin\Encapsulation\Container;

class OrderContainer extends Container
{
    protected function getAllowedClass(): ?string
    {
        return Order::class;
    }
}
