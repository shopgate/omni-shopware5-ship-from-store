<?php

namespace SgateShipFromStore\Components\Order\Serializer;

use Dustin\ImpEx\Serializer\Converter\ArrayList\ListConverter;
use Dustin\ImpEx\Serializer\Converter\EncapsulationConverter;
use Dustin\ImpEx\Serializer\Converter\NormalizerConverter;
use Dustin\ImpEx\Serializer\Converter\Numeric\FloatConverter;
use SgateShipFromStore\Components\Order\Encapsulation\Identifiers;
use SgateShipFromStore\Components\Order\Encapsulation\Option;
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
        $optionNormalizer = new OptionNormalizer($this->metaFile);

        return [
            self::CONVERTERS => [
                'price' => new FloatConverter(),
                'salePrice' => new FloatConverter(FloatConverter::SKIP_NULL),
                'identifiers' => new EncapsulationConverter(Identifiers::class),
                'options' => new ListConverter(
                    new NormalizerConverter(
                        $optionNormalizer,
                        $optionNormalizer,
                        Option::class,
                        null,
                        $this
                    )
                )
            ],
        ];
    }
}
