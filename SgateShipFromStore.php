<?php

namespace SgateShipFromStore;

require_once __DIR__.'/vendor/autoload.php';

use Dustin\ImpEx\ImpExBundle;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SgateShipFromStore extends Plugin
{
    public function install(InstallContext $context)
    {
    }

    public function update(UpdateContext $context)
    {
    }

    public function uninstall(UninstallContext $context)
    {
    }

    public function build(ContainerBuilder $container): void
    {
        (new ImpExBundle())->build($container);
    }
}
