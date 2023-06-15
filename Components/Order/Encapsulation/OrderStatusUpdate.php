<?php

namespace SgateShipFromStore\Components\Order\Encapsulation;

use SgateShipFromStore\Components\Order\OrderStatus;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderStatusUpdate extends OrderRelation
{
    /**
     * @var string
     */
    protected $newStatus;

    public function getConstraints(): array
    {
        $constraints = parent::getConstraints();
        $constraints['newStatus'] = [new NotBlank(), new Choice(['choices' => OrderStatus::getAll()])];

        return $constraints;
    }
}
