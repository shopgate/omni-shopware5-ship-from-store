<?php

namespace SgateShipFromStore\Components\Order\Serializer\Converter;

use Dustin\Encapsulation\EncapsulationInterface;
use Dustin\ImpEx\Serializer\Converter\UnidirectionalConverter;

class LineItemUnitPromoAmountCalculator extends UnidirectionalConverter
{
    public function convert($value, EncapsulationInterface $object, string $path, string $attributeName, ?array $normalizedData = null)
    {
        if ($normalizedData === null) {
            return $value;
        }

        return round($normalizedData['extendedPrice'] - $normalizedData['price'], 2);
    }
}
