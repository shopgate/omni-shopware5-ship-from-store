<?php

namespace Dustin\Encapsulation;

/**
 * Representation of key-based encapsulated data.
 *
 * Provides methods to
 * - access data
 * - check data
 * - access fields
 * - check fields
 * - iterate over all data
 * - serialize encapsulated data into JSON
 */
interface EncapsulationInterface extends \ArrayAccess, \IteratorAggregate, \JsonSerializable
{
    /**
     * Sets the value of a field.
     *
     * @param string $field The name of the field
     * @param mixed  $value The stored value behind the field
     */
    public function set(string $field, $value): void;

    /**
     * Sets multiple values at once.
     *
     * Takes an associative array of data which will be merged into the encapsulation.
     * Each array element will be set as value with the array key as field.
     *
     * @param array<string, mixed> $data An associative array which holds the values to set
     */
    public function setList(array $data): void;

    /**
     * Clears the value or removes the whole field.
     *
     * Unsets the value of a given field.
     * The field can either be removed or set to null.
     *
     * @param string $field The name of the field to unset
     */
    public function unset(string $field): void;

    /**
     * Returns the value of a field.
     *
     * Searches a field and returns the stored value.
     *
     * @param string $field The name of the requested field
     *
     * @return mixed The stored value behind the requested field
     */
    public function get(string $field);

    /**
     * Returns multiple values at once.
     *
     * Takes a list of requested field names and returns an associative array holding the requested values.
     * The field parameter must be a list of strings like <code>['foo', 'bar']</code>.
     *
     * @param array<int, string> $fields The list of requested fields
     *
     * @return array<string, mixed> An associative array holding the requested values
     */
    public function getList(array $fields): array;

    /**
     * Adds a value to a list.
     *
     * The stored value behind the given field should be an array or a {@see} Container.
     * The given value will be added to the existing list.
     *
     * For example:
     *  The stored value behind the field 'myList' is the array <code>['foo', 'bar']</code>.
     *
     * <code>
     *      $encapsulation = new Encapsulation([
     *          'myList' => ['foo', 'bar']
     *      ]);
     *
     *      $encapsulation->add('myList', 'Hello World');
     *      $encapsulation->get('myList');
     * </code>
     *
     * Will return the array <code>['foo', 'bar', 'Hello World']</code>
     *
     * @param string $field The name of the field which should hold an array or {@see} Container
     * @param mixed  $value The value to be added to the list
     */
    public function add(string $field, $value): void;

    /**
     * Adds multiple values to a list at once.
     *
     * Adds all given values to an existing array or {@see} Container
     *
     * @see add()
     *
     * @param string            $field  The name of the field which should hold an array or {@see} Container
     * @param array<int, mixed> $values A list of values to be added to the array or Container
     */
    public function addList(string $field, array $values): void;

    /**
     * Returns wether a field does exist.
     *
     * @param string $field The name of the requested field
     */
    public function has(string $field): bool;

    /**
     * Converts object to array.
     *
     * Returns an associative array holding all data with their field names as array key.
     *
     * @return array All data of the encapsulation
     */
    public function toArray(): array;

    /**
     * Returns a list with all existing fields.
     *
     * @return string[] A list of strings representing all available fields
     */
    public function getFields(): array;
}
