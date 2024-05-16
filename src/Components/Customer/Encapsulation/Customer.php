<?php

namespace SgateShipFromStore\Components\Customer\Encapsulation;

use Dustin\Encapsulation\PropertyEncapsulation;
use SgateShipFromStore\Components\Customer\CustomerExtractionInterface;
use SgateShipFromStore\Framework\ShopIdInterface;

class Customer extends PropertyEncapsulation implements ShopIdInterface, CustomerExtractionInterface
{
    /**
     * @var string
     */
    protected $internalCustomerNumber;

    /**
     * @var string
     */
    protected $externalCustomerNumber;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $emailAddress;

    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string
     */
    protected $shopwareId;

    /**
     * @var string
     */
    protected $salutation;

    /**
     * @var int
     */
    protected $shopId;

    public function getShopgateKey(): string
    {
        return $this->get('internalCustomerNumber') ?? $this->get('emailAddress');
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getCustomer(): Customer
    {
        return $this;
    }
}
