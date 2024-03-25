<?php

namespace SgateShipFromStore\Components\Order\Serializer\Converter;

use Dustin\Encapsulation\EncapsulationInterface;
use Dustin\ImpEx\Serializer\Converter\UnidirectionalConverter;

class LineItemPromoAmountCalculator extends UnidirectionalConverter
{
    /** @var LineItemPriceCalculator */
    private $lineItemPriceCalculator;

    /** @var LineItemExtendedPriceCalculator */
    private $lineItemExtendedPriceCalculator;

    /**
     * @param LineItemPriceCalculator $lineItemPriceCalculator
     * @param LineItemExtendedPriceCalculator $lineItemExtendedPriceCalculator
     */
    public function __construct(LineItemPriceCalculator $lineItemPriceCalculator, LineItemExtendedPriceCalculator $lineItemExtendedPriceCalculator, string ...$flags)
    {
        parent::__construct(...$flags);

        $this->lineItemPriceCalculator = $lineItemPriceCalculator;
        $this->lineItemExtendedPriceCalculator = $lineItemExtendedPriceCalculator;
    }


    public function convert($value, EncapsulationInterface $object, string $path, string $attributeName, ?array $normalizedData = null)
    {
        if ($normalizedData === null) {
            return $value;
        }

        $price = $this->lineItemPriceCalculator->convert($normalizedData['price'], $object, $path, 'price', $normalizedData);
        $extendedPrice = $this->lineItemExtendedPriceCalculator->convert($normalizedData['extendedPrice'], $object, $path, 'extendedPrice', $normalizedData);

        return $price === $extendedPrice ? null : $extendedPrice - $price;
    }
}
