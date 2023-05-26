<?php

namespace SgateShipFromStore\Components\Order\Encapsulation;

use Dustin\ImpEx\Encapsulation\Record;
use SgateShipFromStore\Components\Customer\CustomerExtractionInterface;
use SgateShipFromStore\Components\Customer\Encapsulation\Customer;
use SgateShipFromStore\Components\Order\OrderExtractionInterface;
use SgateShipFromStore\Framework\ShopIdInterface;

class Order extends Record implements ShopIdInterface, CustomerExtractionInterface, OrderExtractionInterface
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

    protected float $taxAmount = 0.0;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var int
     */
    protected $shopId;

    /**
     * @var float
     */
    protected $total;

    /**
     * @var \DateTimeInterface
     */
    protected $submitDate;

    /**
     * @var string
     */
    protected $orderNumber;

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getOrder(): Order
    {
        return $this;
    }
}
