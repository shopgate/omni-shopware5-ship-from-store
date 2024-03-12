<?php

namespace SgateShipFromStore\Components\Order\Serializer;

use Dustin\ImpEx\Serializer\Converter\NormalizerConverter;
use Dustin\ImpEx\Serializer\Converter\Numeric\IntConverter;
use SgateShipFromStore\Components\Order\Encapsulation\Product;
use SgateShipFromStore\Components\Order\Serializer\Converter\LineItemPriceCalculator;
use SgateShipFromStore\Components\Order\Serializer\Converter\LineItemExtendedPriceCalculator;
use SgateShipFromStore\Components\Order\Serializer\Converter\LineItemPromoAmountCalculator;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;
use SgateShipFromStore\Components\Order\Encapsulation\LineItem;

class LineItemNormalizer extends EncapsulationNormalizer
{
    public function getEncapsulationClass(): ?string
    {
        return LineItem::class;
    }

    protected function createDefaultContext(): array
    {
        $productNormalizer = new ProductNormalizer($this->metaFile);
        $lineItemPriceCalculator = new LineItemPriceCalculator();
        $lineItemExtendedPriceCalculator = new LineItemExtendedPriceCalculator();

        return [
            self::CONVERTERS => [
                'quantity' => new IntConverter(),
                'shipToAddressSequenceIndex' => new IntConverter(),
                'price' => $lineItemPriceCalculator,
                'extendedPrice' => $lineItemExtendedPriceCalculator,
                'promoAmount' => new LineItemPromoAmountCalculator(
                    $lineItemPriceCalculator,
                    $lineItemExtendedPriceCalculator
                ),
                'product' => new NormalizerConverter(
                    $productNormalizer,
                    $productNormalizer,
                    Product::class,
                    null,
                    $this
                ),
            ],
        ];
    }
}
