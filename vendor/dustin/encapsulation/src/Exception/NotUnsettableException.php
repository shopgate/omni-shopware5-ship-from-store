<?php

namespace Dustin\Encapsulation\Exception;

use Dustin\Encapsulation\EncapsulationInterface;

class NotUnsettableException extends EncapsulationException
{
    /**
     * @var string
     */
    private $field;

    public function __construct(EncapsulationInterface $encapsulation, string $field)
    {
        $this->field = $field;

        parent::__construct(
            $encapsulation,
            \sprintf("Field '%s' is not unsettable", $field)
        );
    }

    public function getField(): string
    {
        return $this->field;
    }
}
