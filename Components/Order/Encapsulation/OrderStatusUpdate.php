<?php

namespace SgateShipFromStore\Components\Order\Encapsulation;

use Dustin\ImpEx\Encapsulation\Record;
use SgateShipFromStore\Components\Order\OrderNumberInterface;
use SgateShipFromStore\Components\Order\OrderStatus;
use SgateShipFromStore\Framework\ValidatableInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderStatusUpdate extends Record implements ValidatableInterface, OrderNumberInterface
{
    /**
     * @var string
     */
    protected $salesOrderNumber;

    /**
     * @var string
     */
    protected $newStatus;

    public function getConstraints(): array
    {
        return [
            'salesOrderNumber' => [new NotBlank()],
            'newStatus' => [new NotBlank(), new Choice(['choices' => OrderStatus::getAll()])],
        ];
    }

    public function getOrderNumber(): string
    {
        return $this->salesOrderNumber;
    }
}
