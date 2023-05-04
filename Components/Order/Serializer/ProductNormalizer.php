<?php

namespace SgateShipFromStore\Components\Order\Serializer;

use Dustin\ImpEx\Serializer\Normalizer\Converter\EncapsulationConverter;
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
                'identifiers' => new EncapsulationConverter(Identifiers::class),
            ],
        ];
    }
}
