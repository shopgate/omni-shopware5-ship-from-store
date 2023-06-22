<?php

use SgateShipFromStore\Components\Order\Encapsulation\OrderStatusUpdate;
use SgateShipFromStore\Components\Order\Serializer\OrderStatusUpdateNormalizer;
use SgateShipFromStore\Framework\Controller\Api\SequenceInputController;
use SgateShipFromStore\Framework\Encapsulation\RequestData;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;
use SgateShipFromStore\Framework\Serializer\RequestSerializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

class Shopware_Controllers_Api_SgateShipFromStoreUpdateOrder extends SequenceInputController
{
    protected function createRequestData(Request $request): RequestData
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

    protected function getDenormalizer(): EncapsulationNormalizer
    {
        return $this->container->get(OrderStatusUpdateNormalizer::class);
    }

    protected function getSequenceName(): string
    {
        return 'sgate_order_update';
    }
}
