<?php

namespace SgateShipFromStore;

require_once __DIR__.'/vendor/autoload.php';

use Dustin\ImpEx\ImpExBundle;
use SgateShipFromStore\Setup\AttributeHandler;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SgateShipFromStore extends Plugin
{
    public function install(InstallContext $context)
    {
        $this->getAttributeHandler()->updateAttributes();

        parent::install($context);
    }

    public function activate(ActivateContext $context)
    {
        $this->markAllOrdersExported();

        parent::activate($context);
    }

    public function update(UpdateContext $context)
    {
        $this->getAttributeHandler()->updateAttributes();

        parent::update($context);
    }

    public function uninstall(UninstallContext $context)
    {
        if (!$context->keepUserData()) {
            $this->getAttributeHandler()->deleteAttributes();
        }

        parent::uninstall($context);
    }

    public function build(ContainerBuilder $container): void
    {
        (new ImpExBundle())->build($container);

        parent::build($container);
    }

    private function getAttributeHandler()
    {
        return new AttributeHandler(
            $this->container->get('shopware_attribute.crud_service'),
            $this->container->get('models')
        );
    }

    private function markAllOrdersExported()
    {
        $this->container->get('dbal_connection')->executeUpdate('
            UPDATE `s_order_attributes` SET `sgate_ship_from_store_exported` = 1;
            UPDATE `s_user_attributes` SET `sgate_ship_from_store_customer_exported` = 1;
        ');
    }
}
