<?php

namespace SgateShipFromStore\Components\Order;

class OrderStatus
{
    public const NEW = 'new';

    public const OPEN = 'open';

    public const REJECTED = 'rejected';

    public const CANCELED = 'canceled';

    public const READY = 'ready';

    public const FULFILLED = 'fulfilled';

    public const COMPLETED = 'completed';

    public const IN_PROGRESS = 'inProgress';

    public static function getAll(): array
    {
        return [self::NEW, self::OPEN, self::REJECTED, self::CANCELED, self::READY, self::FULFILLED, self::COMPLETED, self::IN_PROGRESS];
    }

    public static function getOrderDetailStatusId(string $shopgateStatus): int
    {
        $status = [
            self::OPEN => 0,
            self::IN_PROGRESS => 1,
            self::REJECTED => 2,
            self::CANCELED => 2,
            self::FULFILLED => 3,
        ][$shopgateStatus] ?? null;

        if ($status === null) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid line item status.', $shopgateStatus));
        }

        return $status;
    }

    private function __construct()
    {
    }
}
