<?php

namespace Dustin\ImpEx\Resource;

interface ResourceObtainInterface
{
    public function receiveResources(ResourceContainer $resources): void;

    public function getNeededResources(): array;
}
