<?php

use SgateShipFromStore\Components\Order\Encapsulation\OrderStatusUpdate;
use SgateShipFromStore\Components\Order\Serializer\OrderStatusUpdateNormalizer;
use SgateShipFromStore\Framework\Encapsulation\RequestData;
use SgateShipFromStore\Framework\Logger;
use SgateShipFromStore\Framework\Sequence\ArrayTransferor;
use SgateShipFromStore\Framework\Sequence\Task\RecordHandlingTaskFactory;
use SgateShipFromStore\Framework\Sequence\Validator;
use SgateShipFromStore\Framework\Serializer\RequestSerializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

class Shopware_Controllers_Api_SgateShipFromStoreUpdateOrder extends Shopware_Controllers_Api_Rest
{
    public function indexAction()
    {
        $logger = $this->container->get(Logger::class);

        $validator = $this->container->get(Validator::class);
        $data = $this->createRequestData($this->Request());

        $logger->error('Incoming order update request: '.json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $validator->validate($data);

        $record = RequestSerializer::convertDataToRecord(
            $data,
            'payload',
            OrderStatusUpdate::class,
            $this->container->get(OrderStatusUpdateNormalizer::class)
        );

        $validator->validate($record);

        $transferor = new ArrayTransferor([$record]);
        $task = $this->container->get(RecordHandlingTaskFactory::class)->buildTask('sgate_order_update', $transferor);

        $task->execute();
    }

    private function createRequestData(Request $request): RequestData
    {
        return RequestData::withConstraints(
            [
                'payload' => [new NotBlank(), new Collection((new OrderStatusUpdate())->getConstraints())],
            ],
            RequestSerializer::decode($request)
        );
    }
}
