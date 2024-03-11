<?php

namespace SgateShipFromStore\Components\Order\Serializer\Converter;

use Dustin\Encapsulation\EncapsulationInterface;
use Dustin\ImpEx\Serializer\Converter\UnidirectionalConverter;
use SgateShipFromStore\Components\Util\LineItem;

class SubTotalCalculator extends UnidirectionalConverter
{
    public function convert($value, EncapsulationInterface $object, string $path, string $attributeName, ?array $normalizedData = null)
    {
        if ($normalizedData === null) {
            return $value;
        }

        $subTotal = 0.0;
        $lineItems = $normalizedData['lineItems'] ?? [];

        foreach ($lineItems as $lineItem) {
            if (LineItem::isProductLineItem($lineItem)) {
                $quantity = (int) $lineItem['quantity'] ?? 1;
                $sum = $quantity * ((float) $lineItem['extendedPrice'] ?? 0.0);
                $subTotal += $sum;
            }
        }

        return $subTotal;
    }
}
