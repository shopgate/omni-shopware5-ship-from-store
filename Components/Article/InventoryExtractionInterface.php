<?php

namespace SgateShipFromStore\Components\Article;

use SgateShipFromStore\Components\Article\Encapsulation\Inventory;

interface InventoryExtractionInterface
{
    public function getInventory(): Inventory;
}
