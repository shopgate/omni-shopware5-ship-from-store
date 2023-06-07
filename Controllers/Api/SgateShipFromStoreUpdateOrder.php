<?php

use SgateShipFromStore\Components\Order\Encapsulation\OrderStatusUpdate;
use SgateShipFromStore\Components\Order\Serializer\OrderStatusUpdateNormalizer;
use SgateShipFromStore\Framework\Sequence\ArrayTransferor;
use SgateShipFromStore\Framework\Sequence\Task\RecordHandlingTaskFactory;
use SgateShipFromStore\Framework\Sequence\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class Shopware_Controllers_Api_SgateShipFromStoreUpdateOrder extends Shopware_Controllers_Api_Rest
{
    public function indexAction()
    {
        $record = $this->buildRecordFromRequest($this->Request());

        $this->container->get(Validator::class)->validate($record);

        $transferor = new ArrayTransferor([$record]);
        $task = $this->container->get(RecordHandlingTaskFactory::class)->buildTask('sgate_order_update', $transferor);
        $task->execute();
    }

    private function buildRecordFromRequest(Request $request): OrderStatusUpdate
    {
        $data = json_decode($request->getContent(), true);
        $normalizer = $this->container->get(OrderStatusUpdateNormalizer::class);

        return $normalizer->denormalize($data, OrderStatusUpdate::class, null, [AbstractNormalizer::GROUPS => ['denormalization']]);
    }
}
