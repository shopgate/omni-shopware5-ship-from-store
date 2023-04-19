<?php

namespace Dustin\Encapsulation;

/**
 * Represents a key-based map of objects of a certain class.
 */
abstract class AbstractObjectMapping extends ArrayEncapsulation
{
    /**
     * Must return the class name.
     *
     * Each object stored in this mapping needs to be instance of the given class.
     */
    abstract protected function getType(): string;

    /**
     * @throws \InvalidArgumentException
     */
    public function set(string $field, $value): void
    {
        $this->validateType($value);

        parent::set($field, $value);
    }

    private function validateType($value)
    {
        $class = $this->getType();

        if (!$value instanceof $class) {
            throw new \InvalidArgumentException(sprintf('Value must be %s. %s given', $class, is_object($value) ? get_class($value) : gettype($value)));
        }
    }
}
