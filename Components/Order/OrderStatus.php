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

    public static function getOrderDetailStatus(int $orderStatus): int
    {
        $status = [
            -1 => 2,
            0 => 0,
            1 => 1,
            2 => 3,
            3 => 1,
            4 => 2,
            5 => 1,
            6 => 1,
            7 => 3,
            8 => 1,
        ][$orderStatus] ?? null;

        if ($status === null) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid order status.', $orderStatus));
        }

        return $status;
    }

    private function __construct()
    {
    }
}
