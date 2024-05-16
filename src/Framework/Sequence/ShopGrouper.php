<?php

namespace SgateShipFromStore\Framework\Sequence;

use Dustin\Encapsulation\Container;
use Dustin\ImpEx\Sequence\DirectPass;
use Dustin\ImpEx\Sequence\Transferor;
use Dustin\ImpEx\Util\Type;
use SgateShipFromStore\Framework\ShopIdInterface;

class ShopGrouper extends DirectPass
{
    public function passFrom(Transferor $transferor): \Generator
    {
        $container = new Container();
        $currentShopId = null;

        foreach ($transferor->passRecords() as $record) {
            if (!$record instanceof ShopIdInterface) {
                throw new \UnexpectedValueException(sprintf('Expected record to be %s. %s given.', ShopIdInterface::class, Type::getDebugType($record)));
            }

            $shopId = $record->getShopId();

            if ($currentShopId === null) {
                $currentShopId = $shopId;
            }

            if ($currentShopId !== $shopId) {
                yield $container;

                $currentShopId = $shopId;
                $container = new Container();
            }

            $container->add($record);
        }

        if (count($container) > 0) {
            yield $container;
        }
    }
}
