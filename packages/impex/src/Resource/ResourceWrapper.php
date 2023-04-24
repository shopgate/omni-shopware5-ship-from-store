<?php

namespace Dustin\ImpEx\Resource;

class ResourceWrapper implements ResourceInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $obtainable;

    /**
     * @var object
     */
    private $resource;

    /**
     * @param object $resource
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($resource, string $name, bool $obtainable)
    {
        if (!is_object($resource)) {
            throw new \InvalidArgumentException(sprintf('Resource must be object. %s given.', gettype($resource)));
        }

        $this->resource = $resource;
        $this->name = $name;
        $this->obtainable = $obtainable;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isObtainable(): bool
    {
        return $this->obtainable;
    }

    /**
     * @return object
     */
    public function getResource()
    {
        return $this->resource;
    }
}
