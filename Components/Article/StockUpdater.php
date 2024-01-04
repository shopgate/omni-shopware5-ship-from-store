<?php

namespace SgateShipFromStore\Components\Article;

use Doctrine\DBAL\Connection;
use Dustin\Encapsulation\ArrayEncapsulation;
use Dustin\ImpEx\Sequence\RecordHandling;
use Dustin\ImpEx\Sequence\Transferor;
use Psr\Log\LoggerInterface;
use SgateShipFromStore\Components\Article\Encapsulation\Inventory;

class StockUpdater implements RecordHandling
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

    public function __construct(
        ArrayEncapsulation $config,
        LoggerInterface $logger,
        Connection $connection
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->connection = $connection;
    }

    public function handle(Transferor $transferor): void
    {
        $productCodeField = $this->config->get('productCode') === 'ean' ? 'ean' : 'ordernumber';
        $logOutput = [];

        $stockUpdateQuery = sprintf('UPDATE `s_articles_details` SET `instock` = :instock WHERE `%s` = :value', $productCodeField);

        foreach ($transferor->passRecords() as $inventory) {
            if (!$inventory instanceof Inventory) {
                throw new \InvalidArgumentException(sprintf('Record must be %s.', Inventory::class));
            }

            $this->connection->executeStatement($stockUpdateQuery, ['instock' => $inventory->get('visible'), 'value' => $inventory->get('productCode')]);
            $logOutput[$inventory->get('productCode')] = $inventory->get('visible');
        }

        $this->logger->info('Stocks successfully updated', $logOutput);
    }
}
