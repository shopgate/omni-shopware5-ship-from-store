<?php

namespace SgateShipFromStore\Framework\Serializer;

use Dustin\Encapsulation\AbstractEncapsulation;
use Dustin\Encapsulation\EncapsulationInterface;
use SgateShipFromStore\Framework\Encapsulation\RequestData;
use SgateShipFromStore\Framework\Util\ArrayUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class RequestSerializer
{
    public static function decode(Request $request): array
    {
        $data = null;

        try {
            $data = json_decode($request->getContent(), true);
        } catch (\Throwable $th) {
        }

        if (!$data) {
            throw new BadRequestHttpException('Invalid JSON');
        }

        return (array) $data;
    }

    public static function convertDataToRecord(RequestData $request, ?string $rootPath = null, string $recordClass, DenormalizerInterface $denormalizer, bool $preserveErrors = true): EncapsulationInterface
    {
        try {
            $data = $request->toArray();

            if ($rootPath !== null) {
                $data = ArrayUtil::extractFromNested($rootPath, $data);
            }

            return $denormalizer->denormalize($data, $recordClass, null, [AbstractNormalizer::GROUPS => ['denormalization']]);
        } catch (\Throwable $th) {
            if ($preserveErrors === false) {
                throw $th;
            }
        }

        if (!is_subclass_of($recordClass, AbstractEncapsulation::class)) {
            throw new \InvalidArgumentException(sprintf('The record class %s does not inherit from %s and therefore cannot be instantiated.', $recordClass, AbstractEncapsulation::class));
        }

        return new $recordClass();
    }
}
