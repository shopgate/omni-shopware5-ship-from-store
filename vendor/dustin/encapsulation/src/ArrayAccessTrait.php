<?php

namespace Dustin\Encapsulation;

/**
 * Provides implementation of {@see} \ArrayAccess methods for encapsulations.
 */
trait ArrayAccessTrait
{
    /**
     * @ignore
     */
    public function offsetExists($offset): bool
    {
        return $this->has(\strval($offset));
    }

    /**
     * @ignore
     */
    public function offsetGet($offset)
    {
        return $this->get(\strval($offset));
    }

    /**
     * @ignore
     */
    public function offsetSet($offset, $value): void
    {
        if (\is_null($offset)) {
            throw new \RuntimeException('You can not set a value to an Encapsulation without offset.');
        }

        $this->set(\strval($offset), $value);
    }

    /**
     * @ignore
     */
    public function offsetUnset($offset): void
    {
        $this->unset(\strval($offset));
    }
}
