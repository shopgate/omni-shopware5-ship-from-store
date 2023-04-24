<?php

namespace Dustin\ImpEx\Resource;

interface ResourceInterface
{
    public function getName(): string;

    public function isObtainable(): bool;
}
