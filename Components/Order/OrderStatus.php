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

    private function __construct()
    {
    }
}
