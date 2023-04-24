<?php

namespace Dustin\ImpEx\Resource;

interface ResourceFactoryInterface
{
    public function getResourceNames(): array;

    public function createResource(string $name): ResourceInterface;
}
