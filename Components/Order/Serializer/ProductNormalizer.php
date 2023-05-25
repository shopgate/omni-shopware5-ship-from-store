<?php

namespace SgateShipFromStore\Components\Order\Serializer;

use Dustin\ImpEx\Serializer\Converter\EncapsulationConverter;
use Dustin\ImpEx\Serializer\Converter\Numeric\FloatConverter;
use SgateShipFromStore\Components\Order\Encapsulation\Identifiers;
use SgateShipFromStore\Components\Order\Encapsulation\Product;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;

class ProductNormalizer extends EncapsulationNormalizer
{
    public function getEncapsulationClass(): ?string
    {
        return Product::class;
    }

    protected function createDefaultContext(): array
    {
        return [
            self::CONVERTERS => [
                'price' => new FloatConverter(),
                'salePrice' => new FloatConverter(FloatConverter::SKIP_NULL),
                'identifiers' => new EncapsulationConverter(Identifiers::class),
            ],
        ];
    }
}
