<?php

namespace SgateShipFromStore\Framework\Controller\Api;

use SgateShipFromStore\Framework\Encapsulation\RequestData;
use SgateShipFromStore\Framework\Sequence\ArrayTransferor;
use SgateShipFromStore\Framework\Sequence\Task\RecordHandlingTaskFactory;
use SgateShipFromStore\Framework\Sequence\Validator;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;
use SgateShipFromStore\Framework\Serializer\RequestSerializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class SequenceInputController extends ApiController
{
    abstract protected function createRequestData(Request $request): RequestData;

    abstract protected function getDenormalizer(): EncapsulationNormalizer;

    abstract protected function getSequenceName(): string;

    public function indexAction()
    {
        $validator = $this->container->get(Validator::class);
        $data = $this->createRequestData($this->Request());

        $validator->validate($data);

        $denormalizer = $this->getDenormalizer();
        $class = $denormalizer->getEncapsulationClass();

        if ($class === null) {
            throw new \RuntimeException('Denormalizer must return target encapsulation class. Got null.');
        }

        if (!$denormalizer->supportsDenormalization($data->toArray(), $class)) {
            throw new \RuntimeException(sprintf('Denormalizer must support denormalization of %s', $class));
        }

        $record = RequestSerializer::convertDataToRecord(
            $data,
            $this->getDataRoot(),
            $class,
            $denormalizer
        );

        $validator->validate($record);

        $this->container->get(RecordHandlingTaskFactory::class)->buildTask(
            $this->getSequenceName(),
            new ArrayTransferor([$record])
        )->execute();
    }

    public function dispatch($action)
    {
        $request = $this->Request();
        $response = $this->Response();

        if (!$this->isMethodAllowed($request->getMethod())) {
            $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

            return;
        }

        if (!$this->isActionAllowed($action)) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);

            return;
        }

        if (strpos($action, 'index') === false) {
            $this->forward('index');
        }

        parent::dispatch($action);
    }

    protected function getDataRoot(): ?string
    {
        return 'payload';
    }

    protected function isActionAllowed(string $action): bool
    {
        return \in_array($action, [
            'indexAction',
            'getAction',
            'putAction',
            'batchAction',
            'postAction',
        ]);
    }

    protected function isMethodAllowed(string $method): bool
    {
        return \in_array($method, [
            'GET',
            'POST',
            'PUT',
        ]);
    }
}
