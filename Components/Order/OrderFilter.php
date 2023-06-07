<?php

namespace SgateShipFromStore\Components\Order;

use Doctrine\DBAL\Connection;
use Dustin\ImpEx\Encapsulation\Record;
use Dustin\ImpEx\Sequence\Filter;
use Dustin\ImpEx\Util\Type;
use Psr\Log\LoggerInterface;

class OrderFilter extends Filter
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Connection $connection,
        LoggerInterface $logger
    ) {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function filter($record): bool
    {
        if (!$record instanceof OrderNumberInterface) {
            throw new \InvalidArgumentException(sprintf('Record must implement %s. Got %s.', OrderNumberInterface::class, Type::getDebugType($record)));
        }

        $orderId = $this->getOrderId($record);

        if ($orderId === null) {
            $this->logger->notice(sprintf('Id for order number %s was not found.', $record->getOrderNumber()));

            return false;
        }

        if ($record instanceof Record) {
            $record->getHeader(true)->set('orderId', $orderId);
        }

        return true;
    }

    protected function getOrderId(OrderNumberInterface $orderNumber): ?int
    {
        $id = $this->connection->executeQuery('SELECT `id` FROM `s_order` WHERE `ordernumber` = :orderNumber', ['orderNumber' => $orderNumber->getOrderNumber()])
            ->fetch(\PDO::FETCH_COLUMN);

        return $id ? $id : null;
    }
}
