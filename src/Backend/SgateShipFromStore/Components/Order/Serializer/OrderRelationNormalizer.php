<?php

namespace SgateShipFromStore\Components\Order\Serializer;

use SgateShipFromStore\Components\Order\Encapsulation\OrderRelation;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;

class OrderRelationNormalizer extends EncapsulationNormalizer
{
    public function getEncapsulationClass(): ?string
    {
        return OrderRelation::class;
    }
}
