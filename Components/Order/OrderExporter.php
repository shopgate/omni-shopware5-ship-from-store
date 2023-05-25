<?php

namespace SgateShipFromStore\Components\Order;

use Dustin\Encapsulation\Container;
use Dustin\ImpEx\Sequence\RecordHandling;
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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class OrderExporter extends InlineRecordHandling implements RecordHandling
{
    /**
     * @var NormalizerInterface
     */
    private $orderNormalizer;

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
        ShopgateSdkRegistry $shopgateSdkRegistry,
        LoggerInterface $logger,
        ModelManager $modelManager,
        ExceptionHandler $exceptionHandler
    ) {
        $this->orderNormalizer = $orderNormalizer;
        $this->shopgateSdkRegistry = $shopgateSdkRegistry;
        $this->logger = $logger;
        $this->modelManager = $modelManager;
        $this->exceptionHandler = $exceptionHandler;
        $this->orderRepository = $modelManager->getRepository(OrderEntity::class);
    }

    public function createShopgateOrders(OrderContainer $orders, int $shopId): void
    {
        $this->prepareOrders($orders);

        $data = (new Container($orders->toArray()))->map(function (Order $order) {
            return $this->orderNormalizer->normalize($order, null, [OrderNormalizer::GROUPS => ['normalization']]);
        })->toArray();

        $orderService = $this->shopgateSdkRegistry->getShopgateSdk($shopId)->getOrderService();
        $task = new CreateShopgateOrdersTask($data, $orderService, $this->logger);

        $validIndizes = array_values(array_keys($orders->toArray()));
        $orderNumbers = [];

        try {
            $result = (array) $task->retry();
            $orderNumbers = $result['orderNumbers'] ?? [];
        } catch (ApiErrorException $exception) {
            $this->exceptionHandler->handle($exception, $shopId);

            foreach ($exception->getErrors() as $error) {
                $index = $error->get('entityIndex');
                unset($validIndizes[$index]);
            }
        } catch (\Throwable $th) {
            $this->exceptionHandler->handle($th, $shopId);
            $validIndizes = [];
        }

        foreach ($validIndizes as $index) {
            $this->updateOrder($orders->getAt($index), $orderNumbers[$index]);
        }
    }

    public function updateOrder(Order $order, string $orderNumber): void
    {
        $id = $order->get('id');
        $orderEntity = $this->orderRepository->findOneBy(['id' => $id]);
        $attribute = $orderEntity->getAttribute() ?? new OrderAttribute();

        $attribute->setOrderId($id);
        $attribute->setSgateShipFromStoreOrderNumber($orderNumber);
        $attribute->setSgateShipFromStoreExported(true);

        $this->modelManager->persist($attribute);
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
        $this->createShopgateOrders($container, $shopId);
    }

    protected function buildContainer(Container $container): Container
    {
        return new OrderContainer(
            $container->map(function (OrderExtractionInterface $source) {
                return $source->getOrder();
            })->toArray()
        );
    }
}
