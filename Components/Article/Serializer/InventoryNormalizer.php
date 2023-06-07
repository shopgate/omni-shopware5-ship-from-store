<?php

namespace SgateShipFromStore\Components\Article\Serializer;

use Dustin\ImpEx\Serializer\Converter\Numeric\IntConverter;
use SgateShipFromStore\Components\Article\Encapsulation\Inventory;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;

class InventoryNormalizer extends EncapsulationNormalizer
{
    public function getEncapsulationClass(): ?string
    {
        return Inventory::class;
    }

    protected function createDefaultContext(): array
    {
        return [
            self::CONVERTERS => [
                'visible' => new IntConverter(),
                'shopId' => new IntConverter(),
            ],
        ];
    }
}
