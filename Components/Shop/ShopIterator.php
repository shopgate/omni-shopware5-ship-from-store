<?php

namespace SgateShipFromStore\Components\Shop;

use Doctrine\DBAL\Connection;

class ShopIterator implements \IteratorAggregate
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var array|null
     */
    private $shops = null;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getIterator(): \Traversable
    {
        $this->loadShops();

        yield from $this->shops;
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
