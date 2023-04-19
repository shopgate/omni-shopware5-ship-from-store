<?php

namespace Dustin\Encapsulation;

use Dustin\Encapsulation\Exception\NotAllowedFieldException;

/**
 * A flexible encapsulation which holds it's data in an array.
 */
abstract class ArrayEncapsulation extends AbstractEncapsulation
{
    private array $data = [];

    public function __construct(array $data = [])
    {
        $this->setList($data);
    }

    /**
     * @throws NotAllowedFieldException
     */
    public function set(string $field, $value): void
    {
        $this->validateField($field);

        $this->data[$field] = $value;
    }

    /**
     * Unsets a value.
     *
     * The value will be unset and the field will no longer be available.
     */
    public function unset(string $field): void
    {
        unset($this->data[$field]);
    }

    /**
     * Returns the value of a field.
     *
     * Returns the stored value or null if the field does not exist.
     * Note that null call also be a stored value behind a field.
     *
     * @return mixed|null
     */
    public function get(string $field)
    {
        if (!$this->has($field)) {
            return null;
        }

        return $this->data[$field];
    }

    public function has(string $field): bool
    {
        return \array_key_exists($field, $this->data);
    }

    public function getFields(): array
    {
        return array_keys($this->data);
    }

    /**
     * Optionally returns a list of allowed fields for this encapsulation.
     *
     * This method can be overwritten to optionally return a list of allowed fields.
     * It's allowed to set values to all fields appearing in the returned list.
     * Otherwise an exception will be thrown.
     *
     * @return string[]|null An array holding a list of strings of which fields are allowed to set
     */
    public function getAllowedFields(): ?array
    {
        return null;
    }

    private function validateField(string $field)
    {
        $allowedFields = $this->getAllowedFields();

        if ($allowedFields === null) {
            return;
        }

        if (!in_array($field, $allowedFields)) {
            throw new NotAllowedFieldException($this, $field);
        }
    }
}
