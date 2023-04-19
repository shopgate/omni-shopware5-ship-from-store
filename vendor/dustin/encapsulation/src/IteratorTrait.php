<?php

namespace Dustin\Encapsulation;

/**
 * Provides method implementations for \IteratorAggregate.
 */
trait IteratorTrait
{
    public function getIterator(): \Traversable
    {
        yield from $this->toArray();
    }
}
