<?php

namespace SgateShipFromStore\Components\Order\Serializer;

use SgateShipFromStore\Components\Order\Encapsulation\OptionValue;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;

class OptionValueNormalizer extends EncapsulationNormalizer
{
    public function getEncapsulationClass(): ?string
    {
        return OptionValue::class;
    }
}
