<?php

namespace SgateShipFromStore\Components\Order;

use Doctrine\DBAL\Connection;
use Dustin\Encapsulation\ArrayEncapsulation;
use Dustin\ImpEx\Sequence\RecordHandling;
use Dustin\ImpEx\Sequence\Transferor;
use Psr\Log\LoggerInterface;
use SgateShipFromStore\Components\Order\Encapsulation\OrderStatusUpdate;

class OrderStatusUpdater implements RecordHandling
{
    /**
     * @var ArrayEncapsulation
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var \sOrder
     */
    private $orderService;

    public function __construct(
        ArrayEncapsulation $config,
        LoggerInterface $logger,
        Connection $connection
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->connection = $connection;
        $this->orderService = Shopware()->Modules()->Order();
    }

    public function handle(Transferor $transferor): void
    {
        foreach ($transferor->passRecords() as $record) {
            $this->updateOrderStatus($record);
        }
    }

    public function updateOrderStatus(OrderStatusUpdate $orderUpdate): void
    {
        $status = $orderUpdate->get('newStatus');
        $statusId = $this->getStatusId($status);

        if ($statusId === null) {
            $this->logger->error(sprintf("Order status '%s' seems not to be mapped.", $status));

            return;
        }

        $this->orderService->setOrderStatus($orderUpdate->getOrderId(), $statusId, true);
        $orderDetails = $this->getOrderDetails($orderUpdate->getOrderId());

        $this->updateOrderDetails($orderDetails, OrderStatus::getOrderDetailStatus($statusId));
    }

    private function getOrderDetails(int $orderId): array
    {
        return $this->connection->createQueryBuilder()
            ->select(['id', 'quantity'])
            ->from('s_order_details')
            ->where('orderID = :orderId')
            ->setParameter('orderId', $orderId)
            ->execute()->fetchAll();
    }

    private function getStatusId(string $status): ?int
    {
        $field = $status.'StatusId';
        $id = $this->config->get($field);

        return $id !== null ? (int) $id : null;
    }

    private function updateOrderDetails(array $orderDetails, int $statusId): void
    {
        $query = $this->connection->createQueryBuilder()
            ->update('s_order_details')
            ->set('status', ':status')
            ->set('shipped', ':quantity')
            ->where('id = :id');

        foreach ($orderDetails as $orderDetail) {
            $orderDetail['status'] = $statusId;
            $query->setParameters($orderDetail)->execute();
        }
    }
}
