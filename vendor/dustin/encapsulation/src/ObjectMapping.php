<?php

namespace Dustin\Encapsulation;

/**
 * A flexible implementation of {@see} AbstractObjectMapping which can be created via static method.
 */
class ObjectMapping extends AbstractObjectMapping
{
    private $objectClass = null;

    /**
     * Prevents creating objects via constructor.
     *
     * @throws \RuntimeException
     */
    public function __construct(array $data = [])
    {
        throw new \RuntimeException('ObjectMappings cannot be created via constructor. Use ObjectMapping::create instead');
    }

    /**
     * Creates a new ObjectMapping which can only hold objects of the given class.
     */
    public static function create(string $class): self
    {
        $mapping = (new \ReflectionClass(static::class))->newInstanceWithoutConstructor();
        $mapping->objectClass = $class;

        return $mapping;
    }

    /**
     * @ignore
     */
    public function __serialize(): array
    {
        return serialize([
            'objectClass' => $this->objectClass,
            'data' => $this->toArray(),
        ]);
    }

    /**
     * @ignore
     */
    public function __unserialize(array $data): void
    {
        $data = unserialize($data);

        $this->objectClass = $data['objectClass'];
        $this->setList($data['data']);
    }

    /**
     * @ignore
     */
    protected function getType(): string
    {
        return $this->objectClass;
    }
}
