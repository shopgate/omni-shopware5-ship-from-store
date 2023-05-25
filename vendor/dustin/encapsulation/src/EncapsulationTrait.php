<?php

namespace Dustin\Encapsulation;

use Dustin\Encapsulation\Exception\NotAnArrayException;

/**
 * Provides basic method implementations for encapsulations.
 */
trait EncapsulationTrait
{
    public function setList(array $data): void
    {
        foreach ($data as $field => $value) {
            $this->set(\strval($field), $value);
        }
    }

    public function getList(array $fields): array
    {
        $result = [];

        foreach ($fields as $fieldName) {
            $result[$fieldName] = $this->get($fieldName);
        }

        return $result;
    }

    /**
     * @throws NotAnArrayException Thrown if the stored value behind the field is not an array an not a {@see} Container
     */
    public function add(string $field, $value): void
    {
        if (!$this->has($field)) {
            $this->set($field, [$value]);

            return;
        }

        $item = $this->get($field);

        if (\is_array($item)) {
            $item[] = $value;
            $this->set($field, $item);

            return;
        } elseif ($item instanceof Container) {
            $item->add($value);

            return;
        }

        throw new NotAnArrayException($this, $field);
    }

    public function addList(string $field, array $values): void
    {
        foreach ($values as $value) {
            $this->add($field, $value);
        }
    }

    public function toArray(): array
    {
        return $this->getList($this->getFields());
    }

    /**
     * @ignore
     */
    public function __serialize(): array
    {
        return array_map('serialize', $this->toArray());
    }

    /**
     * @ignore
     */
    public function __unserialize(array $data): void
    {
        $this->setList(array_map('unserialize', $data));
    }
}
