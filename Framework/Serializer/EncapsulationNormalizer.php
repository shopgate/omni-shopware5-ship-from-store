<?php

namespace SgateShipFromStore\Framework\Serializer;

use Dustin\ImpEx\Serializer\Normalizer\EncapsulationNormalizer as BaseNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class EncapsulationNormalizer extends BaseNormalizer
{
    protected string $metaFile;

    public function __construct(string $metaFile)
    {
        $this->metaFile = $metaFile;

        parent::__construct(
            new ClassMetadataFactory(new YamlFileLoader($metaFile)),
            $this->getNameConverter(),
            null,
            null,
            $this->createDefaultContext()
        );
    }

    protected function getNameConverter(): ?NameConverterInterface
    {
        return null;
    }

    protected function createDefaultContext(): array
    {
        return [];
    }
}
