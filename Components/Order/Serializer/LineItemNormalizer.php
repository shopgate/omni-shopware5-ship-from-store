<?php

namespace SgateShipFromStore\Components\Order\Serializer;

use Dustin\ImpEx\Serializer\Normalizer\Converter\SerializerConverter;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;
use Symfony\Component\Serializer\Serializer;

class LineItemNormalizer extends EncapsulationNormalizer
{
    public function getEncapsulationClass(): ?string
    {
        return LineItem::class;
    }

    protected function createDefaultContext(): array
    {
        return [
            self::CONVERTERS => [
                'product' => new SerializerConverter(
                    new Serializer([new ProductNormalizer($this->metaFile)]),
                    Product::class,
                    $this
                ),
            ],
        ];
    }
}
