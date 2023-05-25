<?php

namespace Dustin\Encapsulation;

use Dustin\Encapsulation\Exception\ImmutableException;

/**
 * Provides method implementations for immutable encapsulations.
 */
trait ImmutableTrait
{
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            parent::set($key, $value);
        }
    }

    /**
     * @throws ImmutableException
     */
    public function set(string $field, $value): void
    {
        throw new ImmutableException($this);
    }

    /**
     * @throws ImmutableException
     */
    public function add(string $field, $value): void
    {
        throw new ImmutableException($this);
    }

    /**
     * @throws ImmutableException
     */
    public function unset(string $field): void
    {
        throw new ImmutableException($this);
    }

    public function isMutable(): bool
    {
        return false;
    }

    /**
     * @ignore
     */
    public function __clone()
    {
        foreach ($this->toArray() as $field => $value) {
            if (\is_object($value)) {
                parent::set($field, clone $value);
            }
        }
    }
}
