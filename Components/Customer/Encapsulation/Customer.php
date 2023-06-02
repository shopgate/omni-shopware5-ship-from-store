<?php

namespace SgateShipFromStore\Components\Customer\Encapsulation;

use Dustin\Encapsulation\PropertyEncapsulation;

class Customer extends PropertyEncapsulation
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

    public function getShopgateKey(): string
    {
        return $this->get('internalCustomerNumber') ?? $this->get('emailAddress');
    }
}
