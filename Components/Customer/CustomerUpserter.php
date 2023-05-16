<?php

namespace SgateShipFromStore\Components\Customer;

use Dustin\Encapsulation\Container;
use Dustin\ImpEx\Sequence\RecordHandling;
use SgateShipFromStore\Components\Customer\Encapsulation\Customer;
use SgateShipFromStore\Components\Customer\Encapsulation\CustomerContainer;
use SgateShipFromStore\Components\ShopgateSdkRegistry;
use SgateShipFromStore\Framework\ExceptionHandler;
use SgateShipFromStore\Framework\Sequence\InlineRecordHandling;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Attribute\Customer as CustomerAttribute;
use Shopware\Models\Customer\Customer as CustomerEntity;

class CustomerUpserter extends InlineRecordHandling implements RecordHandling
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

    private $customerRepository;

    public function __construct(
        ShopgateSdkRegistry $shopgateSdkRegistry,
        ModelManager $modelManager,
        ExceptionHandler $exceptionHandler
    ) {
        $this->shopgateSdkRegistry = $shopgateSdkRegistry;
        $this->modelManager = $modelManager;
        $this->exceptionHandler = $exceptionHandler;
        $this->customerRepository = $modelManager->getRepository(CustomerEntity::class);
    }

    public function upsertCustomers(CustomerContainer $customers, int $shopId): void
    {
        if (count($customers) === 0) {
            return;
        }

        $this->resolveShopgateIds($customers, $shopId);

        $notFoundCustomers = $customers->filter(function (Customer $customer) {
            return $customer->get('id') === null;
        });

        if (count($notFoundCustomers) > 0) {
            $this->createShopgateCustomers($notFoundCustomers, $shopId);
        }

        $this->saveCustomers($customers);
    }

    public function resolveShopgateIds(CustomerContainer $customers, int $shopId): void
    {
        foreach ($customers as $customer) {
            $field = $customer->get('internalCustomerNumber') ? 'internalCustomerNumber' : 'emailAddress';
            $value = $customer->get('internalCustomerNumber') ?? $customer->get('emailAddress');

            $customerFound = $this->searchShopgateCustomer($value, $field, $shopId);

            if ($customerFound !== null) {
                $customer->setList(
                    $customerFound->getList(['id', 'internalCustomerNumber'])
                );
            }
        }
    }

    public function searchShopgateCustomer(string $value, string $filterField, int $shopId): ?Customer
    {
        $customerService = $this->shopgateSdkRegistry->getShopgateSdk($shopId)->getCustomerService();

        $result = $customerService->getCustomers(['filters' => [$filterField => $value], 'limit' => 1]);

        $data = $result['customers'][0] ?? null;

        if ($data === null) {
            return null;
        }

        return $this->buildCustomer($data);
    }

    public function createShopgateCustomers(CustomerContainer $customers, int $shopId): void
    {
        /**
         * Build new non-typed container to hold array data.
         */
        $customers = new Container($customers->toArray());

        $data = $customers->map(function (Customer $customer) {
            return $customer->getList(['firstName', 'lastName', 'emailAddress', 'externalCustomerNumber']);
        })->toArray();

        $customerService = $this->shopgateSdkRegistry->getShopgateSdk($shopId)->getCustomerService();

        try {
            $result = $customerService->addCustomers($data, [], false);
        } catch (\Throwable $th) {
            $this->exceptionHandler->handle($th, $shopId);
        }

        $ids = $result['ids'] ?? [];

        foreach ($ids as $index => $id) {
            $customers->getAt($index)->set('id', $id);
        }
    }

    public function saveCustomers(Container $customers): void
    {
        foreach ($customers as $customer) {
            $id = $customer->get('shopwareId');
            $customerEntity = $this->customerRepository->findOneBy(['id' => $id]);
            $attribute = $customerEntity->getAttribute() ?? new CustomerAttribute();

            $attribute->setCustomerId($id);
            $attribute->setSgateShipFromStoreCustomerNumber($customer->get('internalCustomerNumber'));
            $this->modelManager->persist($attribute);
        }

        $this->modelManager->flush();
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
        $this->upsertCustomers($container, $shopId);
    }

    protected function buildCustomer(array $data): Customer
    {
        $customer = new Customer();
        $data = array_intersect_key($data, array_flip($customer->getFields()));

        $customer->setList($data);

        return $customer;
    }
}
