<?php

namespace SgateShipFromStore\Components\Shop;

use Dustin\Encapsulation\PropertyEncapsulation;

class Shop extends PropertyEncapsulation
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }
}
