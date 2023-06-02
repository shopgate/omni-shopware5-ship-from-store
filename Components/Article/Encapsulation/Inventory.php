<?php

namespace SgateShipFromStore\Components\Article\Encapsulation;

use Dustin\ImpEx\Encapsulation\Record;
use SgateShipFromStore\Components\Article\InventoryExtractionInterface;
use SgateShipFromStore\Components\Article\ProductCodeInterface;
use SgateShipFromStore\Framework\ShopIdInterface;

class Inventory extends Record implements ShopIdInterface, ProductCodeInterface, InventoryExtractionInterface
{
    /**
     * @var string
     */
    protected $productCode;

    /**
     * @var int
     */
    protected $available;

    /**
     * @var int
     */
    protected $shopId;

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function getInventory(): Inventory
    {
        return $this;
    }
}
