<?php

namespace SgateShipFromStore\Components\Order\Encapsulation;

use Dustin\Encapsulation\PropertyEncapsulation;

class Identifiers extends PropertyEncapsulation
{
    /**
     * @var string
     */
    protected $ean;

    /**
     * @var string
     */
    protected $sku;
}
