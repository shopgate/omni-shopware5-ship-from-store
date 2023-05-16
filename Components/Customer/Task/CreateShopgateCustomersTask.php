<?php

namespace SgateShipFromStore\Components\Customer\Task;

use SgateShipFromStore\Framework\Exception\ApiErrorException;
use SgateShipFromStore\Framework\Task\Task;
use Shopgate\ConnectSdk\Service\Customer;

class CreateShopgateCustomersTask extends Task
{
    /**
     * @var array
     */
    private $customers;

    /**
     * @var Customer
     */
    private $customerService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        array $customers,
        Customer $customerService,
        LoggerInterface $logger
    ) {
        $this->customers = $customers;
        $this->customerService = $customerService;
        $this->logger = $logger;
    }

    public function execute()
    {
        $result = $this->customerService->addCustomers($this->customers, [], false);
        $errors = $result['errors'] ?? [];

        $this->logger->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        if (count($errors) > 0) {
            throw ApiErrorException::fromResult('Create customers', $errors);
        }

        return $result;
    }
}
