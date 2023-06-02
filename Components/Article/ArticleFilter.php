<?php

namespace SgateShipFromStore\Components\Article;

use Doctrine\DBAL\Connection;
use Dustin\Encapsulation\ArrayEncapsulation;
use Dustin\ImpEx\Sequence\Filter;
use Dustin\ImpEx\Sequence\Transferor;
use SgateShipFromStore\Framework\ShopIdInterface;

class ArticleFilter extends Filter
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ArrayEncapsulation
     */
    private $config;

    /**
     * @var array
     */
    private $productCodes = [];

    public function __construct(
        Connection $connection,
        ArrayEncapsulation $config
    ) {
        $this->connection = $connection;
        $this->config = $config;
    }

    public function filter($record): bool
    {
        $this->validateRecord($record);

        $shopId = $record->getShopId();
        $codeSet = $this->productCodes[$this->config->get($shopId)->get('productCode') ?? 'sku'];

        return \in_array($record->getProductCode(), $codeSet);
    }

    public function passFrom(Transferor $transferor): \Generator
    {
        $this->loadProductCodes();

        yield from parent::passFrom($transferor);
    }

    private function loadProductCodes(): void
    {
        $result = $this->connection->createQueryBuilder()
            ->select(['`ordernumber`', 'NULLIF(TRIM(`ean`), "") as `ean`'])
            ->from('s_articles_details')
            ->execute()
            ->fetchAll();

        $this->productCodes = [
            'sku' => array_unique(array_column($result, 'ordernumber')),
            'ean' => array_unique(array_filter(array_column($result, 'ean'))),
        ];
    }

    private function validateRecord($record): void
    {
        if (!$record instanceof ShopIdInterface) {
            throw new \InvalidArgumentException(sprintf('Record must implement %s', ShopIdInterface::class));
        }

        if (!$record instanceof ProductCodeInterface) {
            throw new \InvalidArgumentException(sprintf('Record must implement %s', ProductCodeInterface::class));
        }
    }
}
