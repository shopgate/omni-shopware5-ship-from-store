<?php

namespace SgateShipFromStore\Components\Article;

use Doctrine\ORM\EntityRepository;
use Dustin\Encapsulation\ArrayEncapsulation;
use Dustin\Encapsulation\Container;
use SgateShipFromStore\Components\Article\Encapsulation\Inventory;
use SgateShipFromStore\Components\Article\Encapsulation\InventoryContainer;
use SgateShipFromStore\Framework\Sequence\InlineRecordHandling;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Article\Detail as ArticleDetailEntity;

class StockUpdater extends InlineRecordHandling
{
    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var ArrayEncapsulation
     */
    private $config;

    /**
     * @var EntityRepository
     */
    private $articleRepository;

    public function __construct(
        ModelManager $modelManager,
        ArrayEncapsulation $config
    ) {
        $this->modelManager = $modelManager;
        $this->config = $config;
        $this->articleRepository = $modelManager->getRepository(ArticleDetailEntity::class);
    }

    public function updateStocks(InventoryContainer $inventories, int $shopId): void
    {
        $productCodeField = $this->config->get($shopId)->get('productCode') === 'ean' ? 'ean' : 'number';

        /** @var Inventory $inventory */
        foreach ($inventories as $inventory) {
            $article = $this->articleRepository->findOneBy([$productCodeField => $inventory->get('productCode')]);

            if (!$article) {
                continue;
            }

            $article->setInStock($inventory->get('available'));
        }

        $this->modelManager->flush();
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
