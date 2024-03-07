<?php

namespace SgateShipFromStore\Components\Order;

use Doctrine\DBAL\Connection;
use Dustin\Encapsulation\ArrayEncapsulation;
use Dustin\ImpEx\Sequence\RecordHandling;
use Dustin\ImpEx\Sequence\Transferor;
use Psr\Log\LoggerInterface;
use SgateShipFromStore\Components\Order\Encapsulation\OrderStatusUpdate;
use SgateShipFromStore\Framework\ShopgateSdkRegistry;

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
     * @var ShopgateSdkRegistry
     */
    private $shopgateSdkRegistry;

    /**
     * @var \sOrder
     */
    private $orderService;

    public function __construct(
        ArrayEncapsulation $config,
        LoggerInterface $logger,
        Connection $connection,
        ShopgateSdkRegistry $shopgateSdkRegistry
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->connection = $connection;
        $this->shopgateSdkRegistry = $shopgateSdkRegistry;
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

        $lineItems = $this->shopgateSdkRegistry->getShopgateSdk($orderUpdate->getShopId())->getOrderService()->getOrder($orderUpdate->getSalesOrderNumber())['lineItems'] ?? [];

        $this->updateOrderDetails($lineItems, $orderUpdate);
    }

    private function getStatusId(string $status): ?int
    {
        $field = $status.'StatusId';
        $id = $this->config->get($field);

        return $id !== null ? (int) $id : null;
    }

    private function updateOrderDetails(array $lineItems, OrderStatusUpdate $orderUpdate): void
    {
        $updateStatus = $this->connection->createQueryBuilder()
            ->update('s_order_details')
            ->set('status', ':status')
            ->where('id = :id');

        $updateShipped = $this->connection->createQueryBuilder()
            ->update('s_order_details')
            ->set('status', ':status')
            ->set('shipped', ':shipped')
            ->where('id = :id');

        foreach ($lineItems as $lineItem) {
            $params = [
                'status' => OrderStatus::getOrderDetailStatusId($lineItem['status']),
                'id' => $lineItem['code'],
            ];

            $query = $updateStatus;

            if ($orderUpdate->get('newStatus') !== OrderStatus::CANCELED && $lineItem['status'] === OrderStatus::FULFILLED) {
                $params['shipped'] = $lineItem['quantity'];
                $query = $updateShipped;
            }

            $query->setParameters($params)->execute();
        }
    }
}
