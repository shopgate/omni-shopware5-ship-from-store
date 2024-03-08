<?php

namespace SgateShipFromStore\Components\Customer\Encapsulation;

use Dustin\Encapsulation\Container;

class CustomerContainer extends Container
{
    public function uniqueByIdentifier(): self
    {
        $uniqued = [];

        foreach ($this as $customer) {
            $uniqued[$customer->getShopgateKey()] = $customer;
        }

        return new self(array_values($uniqued));
    }

    protected function getAllowedClass(): ?string
    {
        return Customer::class;
    }
}
