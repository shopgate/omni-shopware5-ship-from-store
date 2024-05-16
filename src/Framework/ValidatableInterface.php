<?php

namespace SgateShipFromStore\Framework;

use Dustin\Encapsulation\EncapsulationInterface;
use Symfony\Component\Validator\Constraint;

interface ValidatableInterface extends EncapsulationInterface
{
    /**
     * @return array<string, array<int, Constraint>>
     */
    public function getConstraints(): array;
}
