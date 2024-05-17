<?php

namespace SgateShipFromStore\Components\Order\Encapsulation;

use Dustin\ImpEx\Encapsulation\Record;
use SgateShipFromStore\Framework\ShopIdInterface;
use SgateShipFromStore\Framework\ValidatableInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderRelation extends Record implements ShopIdInterface, ValidatableInterface
{
    /**
     * @var string
     */
    protected $salesOrderNumber;

    /**
     * @var int
     */
    protected $shopId;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var int
     */
    protected $orderId;

    public function getConstraints(): array
    {
        return [
            'salesOrderNumber' => [new NotBlank()],
        ];
    }

    public function getSalesOrderNumber(): string
    {
        return $this->salesOrderNumber;
    }

    public function setSalesOrderNumber(string $salesOrderNumber): void
    {
        $this->salesOrderNumber = $salesOrderNumber;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): void
    {
        $this->shopId = $shopId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): void
    {
        $this->orderId = $orderId;
    }
}
