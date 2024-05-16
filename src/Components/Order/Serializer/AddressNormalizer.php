<?php

namespace SgateShipFromStore\Components\Order\Serializer;

use SgateShipFromStore\Components\Order\Encapsulation\Address;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;

class AddressNormalizer extends EncapsulationNormalizer
{
    public function getEncapsulationClass(): ?string
    {
        return Address::class;
    }
}
