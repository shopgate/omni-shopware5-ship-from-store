<?php

namespace SgateShipFromStore\Components\Customer;

use Doctrine\ORM\EntityRepository;
use Dustin\Encapsulation\Container;
use Dustin\Encapsulation\Encapsulation;
use Psr\Log\LoggerInterface;
use SgateShipFromStore\Components\Customer\Encapsulation\Customer;
use SgateShipFromStore\Components\Customer\Encapsulation\CustomerContainer;
use SgateShipFromStore\Components\Customer\Task\CreateShopgateCustomersTask;
use SgateShipFromStore\Components\Customer\Task\UpdateShopgateCustomerTask;
use SgateShipFromStore\Framework\ShopgateSdkRegistry;
use SgateShipFromStore\Framework\ExceptionHandler;
use SgateShipFromStore\Framework\Sequence\InlineRecordHandling;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Attribute\Customer as CustomerAttribute;
use Shopware\Models\Customer\Customer as CustomerEntity;

class CustomerEmailSyncer extends InlineRecordHandling
{
    /**
     * @var ShopgateSdkRegistry
     */
    private $shopgateSdkRegistry;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var ExceptionHandler
     */
    private $exceptionHandler;

    /**
     * @var EntityRepository
     */
    private $customerRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ShopgateSdkRegistry $shopgateSdkRegistry,
        ModelManager $modelManager,
        ExceptionHandler $exceptionHandler,
        LoggerInterface $logger
    ) {
        $this->shopgateSdkRegistry = $shopgateSdkRegistry;
        $this->modelManager = $modelManager;
        $this->exceptionHandler = $exceptionHandler;
        $this->customerRepository = $modelManager->getRepository(CustomerEntity::class);
        $this->logger = $logger;
    }

    public function syncCustomerEmails(CustomerContainer $customers, int $shopId): void
    {
        if (count($customers) === 0) {
            return;
        }

        $customerService = $this->shopgateSdkRegistry->getShopgateSdk($shopId)->getCustomerService();
        $searchFilters = [];
        foreach ($customers as $customer) {
            $searchFilters[] = $customer->get('internalCustomerNumber');
        }

        $result = $customerService->getCustomers(['filters' => [
            'internalCustomerNumber' => [
                '$in' => $searchFilters
            ]
        ]]);

        if (empty($result) || empty($result['customers'])) {
            return;
        }
        
        foreach ($result['customers'] as $shopgateCustomer) {
            foreach ($customers as $customer) {
                if ($customer->get('internalCustomerNumber') == $shopgateCustomer['internalCustomerNumber'] &&
                    $customer->get('externalCustomerNumber') == $shopgateCustomer['externalCustomerNumber']
                ) {
                    if ($customer->get('emailAddress') != $shopgateCustomer['emailAddress']) {
                        $customerUpdateData = [
                            'id' => $shopgateCustomer['id'],
                            'emailAddress' => $customer->get('emailAddress')
                        ];

                        $task = new UpdateShopgateCustomerTask($customerUpdateData, $customerService, $this->logger);

                        try {
                            $task->retry();
                        } catch (\Throwable $th) {
                            $this->exceptionHandler->handle($th, $shopId);
                        }
                    }

                    $id = $customer->get('shopwareId');
                    $customerEntity = $this->customerRepository->findOneBy(['id' => $id]);
                    $attribute = $customerEntity->getAttribute();
                    $attribute->setSgateShipFromStoreCustomerExported(true);
                    $this->modelManager->persist($attribute);
                    $this->modelManager->flush();
                }
            }
        }
    }

    protected function buildContainer(Container $container): Container
    {
        return new CustomerContainer(
            $container->map(function (CustomerExtractionInterface $source) {
                return $source->getCustomer();
            })->toArray()
        );
    }

    protected function execute(Container $container, int $shopId): void
    {
        $this->syncCustomerEmails($container, $shopId);
    }
}
