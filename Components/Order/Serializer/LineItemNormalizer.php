<?php

namespace SgateShipFromStore\Components\Order\Serializer;

use Dustin\ImpEx\Serializer\Converter\NormalizerConverter;
use Dustin\ImpEx\Serializer\Converter\Numeric\FloatConverter;
use Dustin\ImpEx\Serializer\Converter\Numeric\IntConverter;
use SgateShipFromStore\Components\Order\Encapsulation\Product;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;

class LineItemNormalizer extends EncapsulationNormalizer
{
    public function getEncapsulationClass(): ?string
    {
        return LineItem::class;
    }

    protected function createDefaultContext(): array
    {
        $productNormalizer = new ProductNormalizer($this->metaFile);

        return [
            self::CONVERTERS => [
                'quantity' => new IntConverter(),
                'shipToAddressSequenceIndex' => new IntConverter(),
                'extendedPrice' => new FloatConverter(),
                'price' => new FloatConverter(),
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
