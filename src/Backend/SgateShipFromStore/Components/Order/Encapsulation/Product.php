<?php

namespace SgateShipFromStore\Components\Order\Encapsulation;

use Dustin\Encapsulation\PropertyEncapsulation;

class Product extends PropertyEncapsulation
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
     * @var float
     */
    protected $price;

    /**
     * @var float
     */
    protected $salePrice;

    /**
     * @var string
     */
    protected $currencyCode;

    /**
     * @var Identifiers
     */
    protected $identifiers;

    /**
     * @var string|null
     */
    protected $image;

    protected array $options = [];
}
