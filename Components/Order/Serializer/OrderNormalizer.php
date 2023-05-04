<?php

namespace SgateShipFromStore\Components\Order\Serializer;

use Dustin\ImpEx\Serializer\Normalizer\Converter\ArrayList\ListConverter;
use Dustin\ImpEx\Serializer\Normalizer\Converter\Bool\BoolConverter;
use Dustin\ImpEx\Serializer\Normalizer\Converter\SerializerConverter;
use SgateShipFromStore\Components\Order\Encapsulation\Order;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;
use Symfony\Component\Serializer\Serializer;

class OrderNormalizer extends EncapsulationNormalizer
{
    public function getEncapsulationClass(): ?string
    {
        return Order::class;
    }

    protected function createDefaultContext(): array
    {
        return [
            self::CONVERTERS => [
                'taxExempt' => new BoolConverter(),
                'addressSequences' => new ListConverter(
                    new SerializerConverter(
                        new Serializer([new AddressNormalizer($this->metaFile)]),
                        Address::class,
                        $this
                    )
                ),
                'lineItems' => new ListConverter(
                    new SerializerConverter(
                        new Serializer([new LineItemNormalizer($this->metaFile)]),
                        LineItem::class,
                        $this
                    )
                ),
            ],
        ];
    }
}
