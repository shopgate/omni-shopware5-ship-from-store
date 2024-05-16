<?php

namespace SgateShipFromStore\Framework\Sequence;

use Dustin\Encapsulation\Container;
use Dustin\ImpEx\Sequence\RecordHandling;
use Dustin\ImpEx\Sequence\Transferor;
use Dustin\ImpEx\Util\Type;
use SgateShipFromStore\Framework\ShopIdInterface;

abstract class InlineRecordHandling implements RecordHandling
{
    abstract protected function buildContainer(Container $container): Container;

    abstract protected function execute(Container $container, int $shopId): void;

    public function handle(Transferor $transferor): void
    {
        foreach ($transferor->passRecords() as $container) {
            $this->validateContainer($container);

            $data = $this->buildContainer($container);
            $shopId = $this->getShopId($container);

            $this->execute($data, $shopId);
        }
    }

    protected function validateContainer($container): void
    {
        if (!$container instanceof Container) {
            throw new \UnexpectedValueException(sprintf('Expected record to be %s. Got %s.', Container::class, Type::getDebugType($container)));
        }
    }

    protected function getShopId(Container $container): int
    {
        $shopIds = $container->map(function (ShopIdInterface $source) {
            return $source->getShopId();
        })->unique();

        if (count($shopIds) > 1) {
            throw new \RuntimeException('Cannot handle multiple shops at once.');
        }

        return $shopIds->getAt(0);
    }
}
