<?php

namespace SgateShipFromStore\Components\Customer\Encapsulation;

use Dustin\Encapsulation\Container;

class CustomerContainer extends Container
{
    protected function getAllowedClass(): ?string
    {
        return Customer::class;
    }
}
