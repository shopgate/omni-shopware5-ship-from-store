<?php

namespace SgateShipFromStore\Components\Order\Serializer\Converter;

use Dustin\Encapsulation\EncapsulationInterface;
use Dustin\ImpEx\Serializer\Converter\UnidirectionalConverter;

class DiscountAmountCalculator extends UnidirectionalConverter
{
    public function convert($value, EncapsulationInterface $object, string $path, string $attributeName, ?array $normalizedData = null)
    {
        if ($normalizedData === null) {
            return $value;
        }

        $discountAmount = 0.0;
        $lineItems = $normalizedData['lineItems'] ?? [];

        foreach ($lineItems as $lineItem) {
            if (($lineItem['extendedPrice'] ?? 0.0) < 0) {
                $quantity = $lineItem['quantity'] ?? 1;
                $sum = $quantity * $lineItem['extendedPrice'];

                $discountAmount += $sum;
            }
        }

        return $discountAmount;
    }
}
