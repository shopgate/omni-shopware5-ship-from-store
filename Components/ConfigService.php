<?php

namespace SgateShipFromStore\Components;

use Dustin\Encapsulation\ArrayEncapsulation;
use Dustin\Encapsulation\Encapsulation;
use SgateShipFromStore\Components\Shop\ShopIterator;
use Shopware\Components\Plugin\Configuration\ReaderInterface;

class ConfigService
{
    public const DEFAULT = 'default';

    public const PER_SHOP = 'per_shop';

    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @var ShopIterator
     */
    private $shopIterator;

    /**
     * @var string
     */
    private $pluginName;

    public function __construct(
        ReaderInterface $reader,
        ShopIterator $shopIterator,
        string $pluginName
    ) {
        $this->reader = $reader;
        $this->shopIterator = $shopIterator;
        $this->pluginName = $pluginName;
    }

    public function getConfig($mode = self::DEFAULT): ArrayEncapsulation
    {
        if ($mode === self::DEFAULT) {
            return $this->buildConfig();
        }

        if ($mode !== self::PER_SHOP) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid mode', $mode));
        }

        $configs = [];

        foreach ($this->shopIterator as $shop) {
            $shopId = $shop->getId();
            $configs[$shopId] = $this->buildConfig($shopId);
        }

        return new Encapsulation($configs);
    }

    public function buildConfig(?int $shopId = null): ArrayEncapsulation
    {
        $config = $this->reader->getByPluginName($this->pluginName, $shopId);
        $config = new Encapsulation($config);

        $scopableKeys = ['username', 'password', 'clientId', 'clientSecret'];
        $apiData = [];

        foreach ($scopableKeys as $key) {
            $scopedKey = $key.$config->get('env') === 'dev' ? 'Dev' : 'Prod';

            $apiData[$key] = $config->get($scopedKey);
            $config->unset($key.'Dev');
            $config->unset($key.'Prod');
        }

        $config->setList($apiData);

        return $config;
    }
}
