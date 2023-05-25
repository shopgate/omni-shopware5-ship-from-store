<?php

namespace SgateShipFromStore\Components\Order\Serializer\Converter;

use Dustin\Encapsulation\EncapsulationInterface;
use Dustin\ImpEx\Serializer\Converter\UnidirectionalConverter;

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
            if ($this->isProductLineItem($lineItem)) {
                $quantity = (int) $lineItem['quantity'] ?? 1;
                $sum = $quantity * ((float) $lineItem['extendedPrice'] ?? 0.0);
                $subTotal += $sum;
            }
        }

        return $subTotal;
    }

    private function isProductLineItem(array $lineItem): bool
    {
        $type = $lineItem['type'] ?? false;

        if ($type === false) {
            return false;
        }

        return \in_array($type, [0, 1]);
    }
}
