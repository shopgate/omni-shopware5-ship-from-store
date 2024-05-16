<?php

namespace SgateShipFromStore\Components\Order\Exception;

class OrderNotFoundException extends \Exception
{
    /**
     * @var string
     */
    private $orderNumber;

    public function __construct(string $orderNumber)
    {
        $this->orderNumber = $orderNumber;

        parent::__construct(sprintf("Order '%s' was not found.", $orderNumber));
    }
}
