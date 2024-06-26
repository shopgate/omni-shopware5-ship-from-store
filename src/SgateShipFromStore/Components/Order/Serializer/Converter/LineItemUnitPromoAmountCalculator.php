<?php

namespace SgateShipFromStore\Components\Order\Serializer\Converter;

use Dustin\Encapsulation\EncapsulationInterface;
use Dustin\ImpEx\Serializer\Converter\UnidirectionalConverter;
use SgateShipFromStore\Components\Util\LineItem;

class LineItemUnitPromoAmountCalculator extends UnidirectionalConverter
{
    public function convert($value, EncapsulationInterface $object, string $path, string $attributeName, ?array $normalizedData = null)
    {
        if (
            $normalizedData === null ||
            empty($normalizedData['product']) ||
            !LineItem::isProductLineItem($normalizedData)
        ) {
            return $value;
        }

        $priceBase = $normalizedData['product']['salePrice'] ?? $normalizedData['product']['price'];

        return round($normalizedData['extendedPrice'] - $priceBase, 2);
    }
}
