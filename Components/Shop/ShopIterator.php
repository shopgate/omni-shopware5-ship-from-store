<?php

namespace SgateShipFromStore\Components\Shop;

use Doctrine\DBAL\Connection;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Shop as ShopEntity;

class ShopIterator implements \IteratorAggregate
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var array|null
     */
    private $shops = null;

    private $models = [];

    public function __construct(Connection $connection, ModelManager $modelManager)
    {
        $this->connection = $connection;
        $this->modelManager = $modelManager;
    }

    public function getIterator(): \Traversable
    {
        $this->loadShops();

        yield from $this->shops;
    }

    public function getModel(int $shopId): ShopEntity
    {
        if (!isset($this->models[$shopId])) {
            $this->models[$shopId] = $this->modelManager->getRepository(ShopEntity::class)->findOneBy(['id' => $shopId]);
        }

        return $this->models[$shopId];
    }

    private function loadShops(): void
    {
        if ($this->shops !== null) {
            return;
        }

        $data = $this->connection->executeQuery('SELECT `id`, `name` FROM `s_core_shops`')->fetchAll();

        $this->shops = array_map(function (array $record) {
            return new Shop($record);
        }, $data);
    }
}
