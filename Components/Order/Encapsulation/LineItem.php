<?php

namespace SgateShipFromStore\Components\Order\Encapsulation;

use Dustin\Encapsulation\PropertyEncapsulation;

class LineItem extends PropertyEncapsulation
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var int
     */
    protected $quantity;

    protected string $fulfillmentMethod = 'directShip';

    protected ?string $fulfillmentLocationCode = null;

    protected int $shipToAddressSequenceIndex = 1;

    /**
     * @var string
     */
    protected $currencyCode;

    /**
     * @var float
     */
    protected $extendedPrice;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var float
     */
    protected $unitPromoAmount;

    /**
     * @var float
     */
    protected $promoAmount;

    /**
     * @var Product
     */
    protected $product;

    protected int $type = 0;
}
