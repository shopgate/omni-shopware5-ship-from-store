<?php

namespace SgateShipFromStore\Components\Order\Serializer;

use Dustin\ImpEx\Serializer\Converter\NormalizerConverter;
use SgateShipFromStore\Components\Order\Encapsulation\Option;
use SgateShipFromStore\Components\Order\Encapsulation\OptionValue;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;

class OptionNormalizer extends EncapsulationNormalizer
{
    public function getEncapsulationClass(): ?string
    {
        return Option::class;
    }

    protected function createDefaultContext(): array
    {
        $optionValueNormalizer = new OptionValueNormalizer($this->metaFile);

        return [
            self::CONVERTERS => [
                'value' => new NormalizerConverter(
                    $optionValueNormalizer,
                    $optionValueNormalizer,
                    OptionValue::class,
                    null,
                    $this
                ),
            ],
        ];
    }
}
