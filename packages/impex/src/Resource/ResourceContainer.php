<?php

namespace Dustin\ImpEx\Resource;

use Dustin\ImpEx\Encapsulation\Encapsulation;

class ResourceContainer extends Encapsulation
{
    /**
     * @param object $value
     *
     * @throws \InvalidArgumentException
     */
    public function set(string $field, $value): void
    {
        if (empty(trim($field))) {
            throw new \InvalidArgumentException('Resource name cannot be empty!');
        }

        if (!is_object($value)) {
            throw new \InvalidArgumentException(sprintf('Resources can only be objects. %s given.', gettype($value)));
        }

        parent::set(trim($field), $value);
    }
}
