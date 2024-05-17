<?php

namespace SgateShipFromStore\Components\Order\Encapsulation;

use Dustin\Encapsulation\PropertyEncapsulation;

class Option extends PropertyEncapsulation
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var OptionValue
     */
    protected $value;
}
