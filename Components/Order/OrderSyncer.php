<?php

namespace SgateShipFromStore\Components\Order;

use Dustin\Encapsulation\Container;
use Psr\Log\LoggerInterface;
use SgateShipFromStore\Components\Order\Encapsulation\Order;
use SgateShipFromStore\Components\Order\Encapsulation\OrderContainer;
use SgateShipFromStore\Components\Order\Serializer\OrderNormalizer;
use SgateShipFromStore\Components\Order\Task\CreateShopgateOrdersTask;
use SgateShipFromStore\Components\ShopgateSdkRegistry;
use SgateShipFromStore\Framework\Exception\ApiErrorException;
use SgateShipFromStore\Framework\ExceptionHandler;
use SgateShipFromStore\Framework\Sequence\InlineRecordHandling;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Attribute\Order as OrderAttribute;
use Shopware\Models\Order\Order as OrderEntity;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class OrderSyncer extends InlineRecordHandling
{
    /**
     * @var NormalizerInterface
     */
    private $orderNormalizer;

    /**
     * @var DenormalizerInterface
     */
    private $orderDenormalizer;

    /**
     * @var ShopgateSdkRegistry
     */
    private $shopgateSdkRegistry;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var ExceptionHandler
     */
    private $exceptionHandler;

    private $orderRepository;

    public function __construct(
        NormalizerInterface $orderNormalizer,
        DenormalizerInterface $orderDenormalizer,
        ShopgateSdkRegistry $shopgateSdkRegistry,
        LoggerInterface $logger,
        ModelManager $modelManager,
        ExceptionHandler $exceptionHandler
    ) {
        $this->orderNormalizer = $orderNormalizer;
        $this->orderDenormalizer = $orderDenormalizer;
        $this->shopgateSdkRegistry = $shopgateSdkRegistry;
        $this->logger = $logger;
        $this->modelManager = $modelManager;
        $this->exceptionHandler = $exceptionHandler;
        $this->orderRepository = $modelManager->getRepository(OrderEntity::class);
    }

    public function syncOrders(OrderContainer $orders, int $shopId): void
    {
        $this->resolveOrders($orders, $shopId);

        $newOrders = $orders->filter(function (Order $order) {
            return $order->get('orderNumber') === null;
        });

        $this->prepareOrders($newOrders);

        $data = (new Container($newOrders->toArray()))->map(function (Order $order) {
            return $this->orderNormalizer->normalize($order, null, [OrderNormalizer::GROUPS => ['normalization']]);
        })->toArray();

        $orderService = $this->shopgateSdkRegistry->getShopgateSdk($shopId)->getOrderService();
        $task = new CreateShopgateOrdersTask($data, $orderService, $this->logger);

        $validIndizes = array_values(array_keys($newOrders->toArray()));
        $orderNumbers = [];

        try {
            $result = (array) $task->retry();
            $orderNumbers = $result['orderNumbers'] ?? [];
        } catch (ApiErrorException $exception) {
            $this->exceptionHandler->handle($exception, $shopId);

            foreach ($exception->getErrors() as $error) {
                if ($error->has('entityIndex')) {
                    $index = $error->get('entityIndex');
                    unset($validIndizes[$index]);
                }
            }
        } catch (\Throwable $th) {
            $this->exceptionHandler->handle($th, $shopId);
            $validIndizes = [];
        }

        foreach ($validIndizes as $index) {
            $newOrders->getAt($index)->set('orderNumber', $orderNumbers[$index]);
        }

        $validOrders = $orders->filter(function (Order $order) {
            return $order->get('orderNumber') !== null;
        });

        $this->updateOrders($validOrders);
    }

    public function resolveOrders(OrderContainer $orders, int $shopId): void
    {
        /** @var Order $order */
        foreach ($orders as $order) {
            $orderFound = $this->searchShopgateOrder($order->get('externalCode'), $shopId);
            $order->set('orderNumber', $orderFound ? $orderFound->get('orderNumber') : null);
        }
    }

    public function searchShopgateOrder(string $externalCode, int $shopId): ?Order
    {
        $orderService = $this->shopgateSdkRegistry->getShopgateSdk($shopId)->getOrderService();
        $result = $orderService->getOrders(['filters' => ['externalCode' => $externalCode], 'limit' => 1]);

        $data = $result['orders'][0] ?? null;

        if ($data === null) {
            return null;
        }

        return $this->buildOrder($data);
    }

    public function updateOrders(OrderContainer $orders): void
    {
        foreach ($orders as $order) {
            $id = $order->get('id');
            $orderEntity = $this->orderRepository->findOneBy(['id' => $id]);
            $attribute = $orderEntity->getAttribute() ?? new OrderAttribute();

            $attribute->setOrderId($id);
            $attribute->setSgateShipFromStoreOrderNumber($order->get('orderNumber'));
            $attribute->setSgateShipFromStoreExported(true);

            $this->modelManager->persist($attribute);
        }

        $this->modelManager->flush();
    }

    protected function prepareOrders(OrderContainer $orders): void
    {
        foreach ($orders as $order) {
            $order->setList([
                'customerId' => $order->get('customer')->get('id'),
                'status' => 'open',
            ]);
        }
    }

    protected function execute(Container $container, int $shopId): void
    {
        $this->syncOrders($container, $shopId);
    }

    protected function buildContainer(Container $container): Container
    {
        return new OrderContainer(
            $container->map(function (OrderExtractionInterface $source) {
                return $source->getOrder();
            })->toArray()
        );
    }

    protected function buildOrder(array $data): Order
    {
        return $this->orderDenormalizer->denormalize($data, Order::class, null, [AbstractNormalizer::GROUPS => ['denormalization']]);
    }
}
