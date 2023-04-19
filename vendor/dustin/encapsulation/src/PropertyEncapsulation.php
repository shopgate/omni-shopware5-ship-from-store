<?php

namespace Dustin\Encapsulation;

use Dustin\Encapsulation\Exception\NotUnsettableException;
use Dustin\Encapsulation\Exception\PropertyNotExistsException;
use Dustin\Encapsulation\Exception\StaticException;

/**
 * An encapsulation which holds it's data in object properties.
 *
 * Uses reflection ({@see} https://www.php.net/manual/en/book.reflection.php) to detect properties and methods.
 * The encapsulation methods set(), get(), and add() will call their corresponding setter-, getter- or adder-method if available.
 * Static properties and methods are not supported.
 */
abstract class PropertyEncapsulation extends AbstractEncapsulation
{
    public function __construct(array $data = [])
    {
        $this->setList($data);
    }

    /**
     * Sets a property's value or calls a found setter-method.
     *
     * Searches for an existing non-static setter-method first. The name of the method must be 'set' + the field name with first letter uppercase e.g.:
     * $field = 'productNumber', setter-method: setProductNumber
     * If no setter-method was found the value will be assigned to the property using reflection.
     * The property must be visible in the current scope.
     *
     * @param string $field The name of the object property
     * @param mixed  $value The value to assign to the property
     *
     * @throws StaticException            Thrown if no setter method was found and $field is a static property
     * @throws PropertyNotExistsException Thrown if no setter method and no non-static property was found
     */
    public function set(string $field, $value): void
    {
        $reflectionObject = new \ReflectionObject($this);

        $setterMethodName = 'set'.\ucfirst($field);

        if (
            $reflectionObject->hasMethod($setterMethodName) &&
            !$reflectionObject->getMethod($setterMethodName)->isStatic()
        ) {
            $this->$setterMethodName($value);

            return;
        }

        if (!$reflectionObject->hasProperty($field)) {
            throw new PropertyNotExistsException($this, $field);
        }

        /** @var \ReflectionProperty $reflectionProperty */
        $reflectionProperty = $reflectionObject->getProperty($field);

        if ($reflectionProperty->isStatic()) {
            throw new StaticException($this, $field);
        }

        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this, $value);
    }

    /**
     * Sets the value of a property to null.
     *
     * @param string $field The name of the property
     *
     * @throws PropertyNotExistsException Thrown if the property does not exist
     * @throws StaticException            Thrown if the given property is static
     * @throws NotUnsettableException     Thrown if the given property is typed and not nullable
     */
    public function unset(string $field): void
    {
        $reflectionObject = new \ReflectionObject($this);

        if (!$reflectionObject->hasProperty($field)) {
            throw new PropertyNotExistsException($this, $field);
        }

        /** @var \ReflectionProperty $property */
        $property = $reflectionObject->getProperty($field);
        $property->setAccessible(true);

        if ($property->isStatic()) {
            throw new StaticException($this, $field);
        }

        if (!$property->isInitialized($this)) {
            return;
        }

        if ($property->hasType() && !$property->getType()->allowsNull()) {
            throw new NotUnsettableException($this, $field);
        }

        $property->setValue($this, null);
    }

    /**
     * Returns a property's value or the result of a found getter-method.
     *
     * Searches for an existing non-static getter-method. The method name must be 'get' + the field name with first letter uppercase, e.g.:
     * $field = 'productNumber', getter-method: getProductNumber
     * If no getter-method was found the value will be fetched from the property using reflection. If the property is not initialized null will be returned.
     * The property must be visible in the current scope.
     *
     * @param string $field The name of the object property
     *
     * @return mixed|null The result of the getter-method, the property's  value or null if property is not initialized
     *
     * @throws PropertyNotExistsException Thrown if no non-static getter-method or property was found
     * @throws StaticException            Thrown if no non-static getter-method was found and the property is static
     */
    public function get(string $field)
    {
        $reflectionObject = new \ReflectionObject($this);
        $getterMethodName = 'get'.\ucfirst($field);

        if (
            $reflectionObject->hasMethod($getterMethodName) &&
            !$reflectionObject->getMethod($getterMethodName)->isStatic()
        ) {
            return $this->$getterMethodName();
        }

        if (!$reflectionObject->hasProperty($field)) {
            throw new PropertyNotExistsException($this, $field);
        }

        /** @var \ReflectionProperty $reflectionProperty */
        $reflectionProperty = $reflectionObject->getProperty($field);

        if ($reflectionProperty->isStatic()) {
            throw new StaticException($this, $field);
        }

        $reflectionProperty->setAccessible(true);

        if ($reflectionProperty->hasType() && !$reflectionProperty->isInitialized($this)) {
            return null;
        }

        return $reflectionProperty->getValue($this);
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     * Searches for an existing non-static adder-method. The method name must be 'add' + the field name with first letter uppercase, e.g.:
     * $field = 'categories', adder-method: addCategories
     * If no adder-method was found the value will be added to the list via reflection
     * The property must be visible in the current scope.
     *
     * @param string $field The property name
     * @param mixed  $value The value to be added to an array or container
     */
    public function add(string $field, $value): void
    {
        $reflectionObject = new \ReflectionObject($this);
        $adderMethodName = 'add'.\ucfirst($field);

        if (
            $reflectionObject->hasMethod($adderMethodName) &&
            !$reflectionObject->getMethod($adderMethodName)->isStatic()
        ) {
            $this->$adderMethodName($value);

            return;
        }

        parent::add($field, $value);
    }

    /**
     * Return wether a property exists and is not static.
     */
    public function has(string $field): bool
    {
        $reflectionObject = new \ReflectionObject($this);

        if (!$reflectionObject->hasProperty($field)) {
            return false;
        }

        return !$reflectionObject->getProperty($field)->isStatic();
    }

    /**
     * Returns a list of all non-static properties.
     */
    public function getFields(): array
    {
        $fields = [];
        $reflectionObject = new \ReflectionObject($this);

        /** @var \ReflectionProperty $reflectionProperty */
        foreach ($reflectionObject->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->isStatic()) {
                continue;
            }

            $fields[] = $reflectionProperty->getName();
        }

        return $fields;
    }

    /**
     * Returns wether all properties are empty.
     *
     * Empty values are empty arrays and null-values
     */
    public function isEmpty(): bool
    {
        return empty(\array_filter($this->toArray(), function ($value) {
            return \is_array($value) ? !empty($value) : $value !== null;
        }));
    }
}
