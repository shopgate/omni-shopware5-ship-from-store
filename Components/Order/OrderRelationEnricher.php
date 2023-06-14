<?php

namespace SgateShipFromStore\Components\Order;

use Doctrine\DBAL\Connection;
use Dustin\ImpEx\Sequence\Filter;
use Psr\Log\LoggerInterface;
use SgateShipFromStore\Components\Order\Encapsulation\OrderRelation;
use SgateShipFromStore\Components\Order\Exception\OrderNotFoundException;

class OrderRelationEnricher extends Filter
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $filter = true;

    public function __construct(
        Connection $connection,
        LoggerInterface $logger,
        bool $filter = true
    ) {
        $this->connection = $connection;
        $this->logger = $logger;
        $this->filter = $filter;
    }

    public function filter($record): bool
    {
        try {
            $this->enrichOrderData($record);
        } catch (OrderNotFoundException $exception) {
            $this->logger->notice(sprintf('Id for order number %s was not found.', $record->getSalesOrderNumber()));

            return !$this->filter;
        }

        return true;
    }

    public function enrichOrderData(OrderRelation $record): void
    {
        $data = $this->getOrderData($record);

        if ($data === null) {
            throw new OrderNotFoundException($record->getSalesOrderNumber());
        }

        $record->setShopId((int) $data['shopId']);
        $record->setOrderId((int) $data['id']);
        $record->setEmail((string) $data['email']);
    }

    protected function getOrderData(OrderRelation $record): ?array
    {
        $sql = 'SELECT `order`.`id`, `order`.`language` as `shopId`, `customer`.`email`
            FROM `s_order` `order`
            LEFT JOIN `s_user` `customer` ON `order`.`userID` = `customer`.`id`
            WHERE `order`.`ordernumber` = :orderNumber';

        $data = $this->connection->fetchAssociative($sql, ['orderNumber' => $record->getSalesOrderNumber()]);

        return $data ? (array) $data : null;
    }
}
