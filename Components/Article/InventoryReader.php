<?php

namespace SgateShipFromStore\Components\Article;

use Dustin\Encapsulation\ArrayEncapsulation;
use Dustin\Encapsulation\Encapsulation;
use Dustin\ImpEx\Serializer\Converter\DateTime\DateTimeConverter;
use Dustin\ImpEx\Util\Type;
use SgateShipFromStore\Components\Article\Encapsulation\StockIdentifier;
use SgateShipFromStore\Components\Shop\ShopIterator;
use SgateShipFromStore\Framework\ExceptionHandler;
use SgateShipFromStore\Framework\Reader\Reader;
use SgateShipFromStore\Framework\ShopgateSdkRegistry;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class InventoryReader extends Reader
{
    /**
     * @var ShopgateSdkRegistry
     */
    private $shopgateSdkRegistry;

    /**
     * @var ArrayEncapsulation
     */
    private $config;

    /**
     * @var ShopIterator
     */
    private $shopIterator;

    /**
     * @var DateTimeConverter
     */
    private $dateTimeConverter;

    /**
     * @var ExceptionHandler
     */
    private $exceptionHandler;

    /**
     * @var ArrayEncapsulation
     */
    private $cache;

    public function __construct(
        DenormalizerInterface $denormalizer,
        ShopgateSdkRegistry $shopgateSdkRegistry,
        ArrayEncapsulation $config,
        ShopIterator $shopIterator,
        DateTimeConverter $dateTimeConverter,
        ExceptionHandler $exceptionHandler
    ) {
        $this->shopgateSdkRegistry = $shopgateSdkRegistry;
        $this->config = $config;
        $this->shopIterator = $shopIterator;
        $this->dateTimeConverter = $dateTimeConverter;
        $this->exceptionHandler = $exceptionHandler;

        $this->cache = new Encapsulation();

        parent::__construct($denormalizer);
    }

    public function getNextUpIdentifiers(): \Generator
    {
        $this->cache = new Encapsulation();

        foreach ($this->shopIterator as $shop) {
            $shopId = $shop->get('id');
            $filter = $this->buildFilter($shopId);
            $catalogService = $this->shopgateSdkRegistry->getShopgateSdk($shopId)->getCatalogService();

            try {
                $result = $catalogService->getCumulatedInventories(['filters' => $filter]);
            } catch (\Throwable $th) {
                $this->exceptionHandler->handle($th, $shopId);

                continue;
            }

            $cumulatedInventories = $result['cumulatedInventories'] ?? [];

            foreach ($cumulatedInventories as $inventory) {
                $identifier = new StockIdentifier([
                    'productCode' => $inventory['productCode'],
                    'shopId' => $shopId,
                ]);

                $inventory = array_merge($inventory, ['shopId' => $shopId]);

                $this->cache->set($identifier->toString(), $inventory);

                yield $identifier;
            }
        }
    }

    /**
     * @param StockIdentifier $identifier
     */
    protected function read($identifier): ?array
    {
        $this->validateIdentifier($identifier);
        $key = $identifier->toString();

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        return $this->getCumulatedInventory($identifier->get('productCode'), $identifier->get('shopId'));
    }

    protected function getCumulatedInventory(string $productCode, int $shopId): ?array
    {
        $catalogService = $this->shopgateSdkRegistry->getShopgateSdk($shopId)->getCatalogService();
        $inventories = $catalogService->getProductInventories([['productCode' => $productCode]]);

        return $inventories['productInventories'][0] ?? null;
    }

    private function buildFilter(int $shopId): array
    {
        $interval = (int) $this->config->get($shopId)->get('stockUpdateInterval');
        $dateTime = date_create('@'.strtotime('-'.$interval.' minutes'));

        $dateTime = $this->dateTimeConverter->normalize($dateTime, new Encapsulation(), '', '');

        return [
            'updateDate' => [
                '$gte' => $dateTime,
            ],
        ];
    }

    private function validateIdentifier($identifier): void
    {
        if (!$identifier instanceof StockIdentifier) {
            throw new \RuntimeException(sprintf('Expected identifier to be %s. Got %s.', StockIdentifier::class, Type::getDebugType($identifier)));
        }

        if ($identifier->get('productCode') === null || $identifier->get('shopId') === null) {
            throw new \RuntimeException('Identifier seems to be incomplete.');
        }
    }
}
