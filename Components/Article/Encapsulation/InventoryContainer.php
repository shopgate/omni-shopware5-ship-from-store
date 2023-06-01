<?php

namespace SgateShipFromStore\Components\Article\Encapsulation;

use Dustin\Encapsulation\Container;

class InventoryContainer extends Container
{
    protected function getAllowedClass(): ?string
    {
        return Inventory::class;
    }
}
