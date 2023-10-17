<?php

namespace SgateShipFromStore\Components\Article;

use Doctrine\DBAL\Connection;
use Dustin\Encapsulation\ArrayEncapsulation;
use Dustin\Encapsulation\Container;
use Psr\Log\LoggerInterface;
use SgateShipFromStore\Components\Article\Encapsulation\InventoryContainer;
use SgateShipFromStore\Framework\Sequence\InlineRecordHandling;

class StockUpdater extends InlineRecordHandling
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

    public function updateStocks(InventoryContainer $inventories, int $shopId): void
    {
        $productCodeField = $this->config->get($shopId)->get('productCode') === 'ean' ? 'ean' : 'ordernumber';
        $logOutput = [];

        $stockUpdateQuery = sprintf('UPDATE `s_articles_details` SET `instock` = :instock WHERE `%s` = :value', $productCodeField);

        foreach ($inventories as $inventory) {
            $this->connection->executeStatement($stockUpdateQuery, ['instock' => $inventory->get('visible'), 'value' => $inventory->get('productCode')]);
            $logOutput[$inventory->get('productCode')] = $inventory->get('visible');
        }

        $this->logger->info('Stocks successfully updated', $logOutput);
    }

    protected function buildContainer(Container $container): Container
    {
        return new InventoryContainer(
            $container->map(function (InventoryExtractionInterface $source) {
                return $source->getInventory();
            })->toArray()
        );
    }

    protected function execute(Container $container, int $shopId): void
    {
        $this->updateStocks($container, $shopId);
    }
}
