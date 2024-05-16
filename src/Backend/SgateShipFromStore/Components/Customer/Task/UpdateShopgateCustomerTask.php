<?php

namespace SgateShipFromStore\Components\Customer\Task;

use SgateShipFromStore\Framework\Exception\ApiErrorException;
use SgateShipFromStore\Framework\Exception\CancelRetryException;
use SgateShipFromStore\Framework\Task\Task;
use Shopgate\ConnectSdk\Service\Customer;
use Psr\Log\LoggerInterface;

class UpdateShopgateCustomerTask extends Task
{
    /**
     * @var array
     */
    private $customer;

    /**
     * @var Customer
     */
    private $customerService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        array $customer,
        Customer $customerService,
        LoggerInterface $logger
    ) {
        $this->customer = $customer;
        $this->customerService = $customerService;
        $this->logger = $logger;
    }

    public function execute()
    {
        $updateData = [
            'emailAddress' => $this->customer['emailAddress']
        ];

        $result = $this->customerService->updateCustomer($this->customer['id'], $updateData, [], false);
        $errors = $result['errors'] ?? [];

        $this->logger->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        if (count($errors) > 0) {
            $exception = ApiErrorException::fromResult('Update customer', $errors);
            throw new CancelRetryException($exception);
        }

        return $result;
    }
}
