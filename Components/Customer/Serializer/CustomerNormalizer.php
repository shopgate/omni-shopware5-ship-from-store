<?php

namespace SgateShipFromStore\Components\Customer\Serializer;

use Dustin\ImpEx\Serializer\Converter\Bool\BoolConverter;
use SgateShipFromStore\Components\Customer\Encapsulation\Customer;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;

class CustomerNormalizer extends EncapsulationNormalizer
{
    public function getEncapsulationClass(): ?string
    {
        return Customer::class;
    }

    protected function createDefaultContext(): array
    {
        return [
            self::CONVERTERS => [
                'isAnonymous' => new BoolConverter(),
            ],
        ];
    }
}
