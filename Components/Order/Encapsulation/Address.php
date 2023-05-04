<?php

namespace SgateShipFromStore\Components\Order\Address;

use Dustin\Encapsulation\PropertyEncapsulation;

class Address extends PropertyEncapsulation
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string|null
     */
    protected $company;

    /**
     * @var string
     */
    protected $address1;

    /**
     * @var string|null
     */
    protected $address2;

    /**
     * @var string|null
     */
    protected $address3;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string|null
     */
    protected $region;

    /**
     * @var string
     */
    protected $postalCode;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string|null
     */
    protected $phone;

    /**
     * @var string
     */
    protected $emailAddress;
}
