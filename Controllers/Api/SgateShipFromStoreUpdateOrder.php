<?php

use SgateShipFromStore\Components\Order\Encapsulation\OrderStatusUpdate;
use SgateShipFromStore\Components\Order\Serializer\OrderStatusUpdateNormalizer;
use SgateShipFromStore\Framework\Controller\Api\ApiController;
use SgateShipFromStore\Framework\Encapsulation\RequestData;
use SgateShipFromStore\Framework\Sequence\ArrayTransferor;
use SgateShipFromStore\Framework\Sequence\Task\RecordHandlingTaskFactory;
use SgateShipFromStore\Framework\Sequence\Validator;
use SgateShipFromStore\Framework\Serializer\RequestSerializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

class Shopware_Controllers_Api_SgateShipFromStoreUpdateOrder extends ApiController
{
    public function indexAction()
    {
        $validator = $this->container->get(Validator::class);
        $data = $this->createRequestData($this->Request());

        $validator->validate($data);

        $record = RequestSerializer::convertDataToRecord(
            $data,
            'payload',
            OrderStatusUpdate::class,
            $this->container->get(OrderStatusUpdateNormalizer::class)
        );

        $validator->validate($record);

        $this->container->get(RecordHandlingTaskFactory::class)->buildTask(
            'sgate_order_update',
            new ArrayTransferor([$record])
        )->execute();
    }

    private function createRequestData(Request $request): RequestData
    {
        return RequestData::withConstraints(
            [
                'payload' => [
                    new NotBlank(),
                    new Collection([
                        'fields' => (new OrderStatusUpdate())->getConstraints(),
                        'allowExtraFields' => true,
                    ]),
                ],
            ],
            RequestSerializer::decode($request)
        );
    }
}
