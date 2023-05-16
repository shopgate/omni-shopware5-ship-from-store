<?php

namespace SgateShipFromStore\Components;

use Dustin\Encapsulation\ArrayEncapsulation;
use Shopgate\ConnectSdk\ShopgateSdk;

class ShopgateSdkRegistry
{
    /**
     * @var ArrayEncapsulation
     */
    private $defaultConfig;

    /**
     * @var ArrayEncapsulation
     */
    private $configPerShop;

    /**
     * @var array
     */
    private $sdks = [];

    public function __construct(
        ArrayEncapsulation $defaultConfig,
        ArrayEncapsulation $configPerShop
    ) {
        $this->defaultConfig = $defaultConfig;
        $this->configPerShop = $configPerShop;
    }

    public function getShopgateSdk(?int $shopId = null): ShopgateSdk
    {
        $index = $shopId ?? 'default';

        if (!isset($this->sdks[$index])) {
            $this->sdks[$index] = $this->build($shopId);
        }

        return $this->sdks[$index];
    }

    protected function build(?int $shopId = null): ShopgateSdk
    {
        $config = $shopId === null ? $this->defaultConfig : ($this->configPerShop[$shopId] ?? new ArrayEncapsulation());
        $configData = $config->getList(array_merge(ShopgateSdk::REQUIRED_CONFIG_FIELDS, ['env']));

        return new ShopgateSdk($configData);
    }
}
