<?php

namespace Dustin\Encapsulation;

/**
 * Provides method implementations for \JsonSerializable.
 */
trait JsonSerializableTrait
{
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
