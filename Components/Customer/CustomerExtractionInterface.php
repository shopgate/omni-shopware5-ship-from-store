<?php

namespace SgateShipFromStore\Components\Customer;

use SgateShipFromStore\Components\Customer\Encapsulation\Customer;

interface CustomerExtractionInterface
{
    public function getCustomer(): Customer;
}
