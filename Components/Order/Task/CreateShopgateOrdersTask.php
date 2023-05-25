<?php

namespace SgateShipFromStore\Components\Order\Task;

use Psr\Log\LoggerInterface;
use SgateShipFromStore\Framework\Exception\ApiErrorException;
use SgateShipFromStore\Framework\Exception\CancelRetryException;
use SgateShipFromStore\Framework\Task\Task;
use Shopgate\ConnectSdk\Service\Order;

class CreateShopgateOrdersTask extends Task
{
    /**
     * @var array
     */
    private $orders;

    /**
     * @var Order
     */
    private $orderService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        array $orders,
        Order $orderService,
        LoggerInterface $logger
    ) {
        $this->orders = $orders;
        $this->orderService = $orderService;
        $this->logger = $logger;
    }

    public function execute()
    {
        $result = $this->orderService->addOrders($this->orders);
        $errors = $result['errors'] ?? [];

        $this->logger->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        if (count($errors) > 0) {
            $exception = ApiErrorException::fromResult('Create orders', $errors);
            throw new CancelRetryException($exception);
        }

        return $result;
    }
}
