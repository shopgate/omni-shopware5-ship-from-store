<?php

namespace SgateShipFromStore\Framework\Encapsulation;

use Dustin\Encapsulation\ArrayEncapsulation;
use SgateShipFromStore\Framework\ValidatableInterface;

class RequestData extends ArrayEncapsulation implements ValidatableInterface
{
    /**
     * @var array
     */
    private $constraints = [];

    public static function withConstraints(array $constraints, array $initData = []): self
    {
        $request = new static($initData);
        $request->constraints = $constraints;

        return $request;
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }
}
