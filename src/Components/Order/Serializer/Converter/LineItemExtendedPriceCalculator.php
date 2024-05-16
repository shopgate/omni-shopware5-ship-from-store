<?php

namespace SgateShipFromStore\Components\Order\Serializer\Converter;

use Dustin\Encapsulation\EncapsulationInterface;
use Dustin\ImpEx\Serializer\Converter\UnidirectionalConverter;
use SgateShipFromStore\Components\Util\LineItem;

class LineItemExtendedPriceCalculator extends UnidirectionalConverter
{
    public function convert($value, EncapsulationInterface $object, string $path, string $attributeName, ?array $normalizedData = null)
    {
        if (
            $normalizedData === null ||
            empty($normalizedData['price']) ||
            !LineItem::isProductLineItem($normalizedData)
        ) {
            return $value;
        }

        return round($normalizedData['quantity'] * $normalizedData['extendedPrice'], 2);
    }
}
