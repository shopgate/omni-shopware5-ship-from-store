<?php

namespace SgateShipfromStore\Components\Order\Encapsulation;

use Dustin\ImpEx\Encapsulation\Record;

class Order extends Record
{
    /**
     * @var string
     */
    protected $externalCode;

    /**
     * @var string
     */
    protected $shopCode;

    protected string $type = 'standard';

    /**
     * @var string
     */
    protected $customerId;

    /**
     * @var string
     */
    protected $externalCustomerNumber;

    /**
     * @var string
     */
    protected $localeCode;

    /**
     * @var string
     */
    protected $currencyCode;

    /**
     * @var string
     */
    protected $status;

    protected bool $taxExempt = false;

    /**
     * @var string|null
     */
    protected $notes = null;

    /**
     * @var string|null
     */
    protected $specialInstructions = null;

    protected array $addressSequences = [];

    /**
     * @var int
     */
    protected $primaryBillToAddressSequenceIndex;

    /**
     * @var int
     */
    protected $primaryShipToAddressSequenceIndex;

    /**
     * @var float
     */
    protected $shippingTotal;

    /**
     * @var string
     */
    protected $domain;

    protected bool $imported = true;

    /**
     * @var float
     */
    protected $subTotal;

    protected float $discountAmount = 0.0;

    protected array $lineItems = [];
}
