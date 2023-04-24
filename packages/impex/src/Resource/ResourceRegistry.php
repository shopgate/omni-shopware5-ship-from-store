<?php

namespace Dustin\ImpEx\Resource;

use Dustin\ImpEx\Resource\Exception\MultipleFactoryException;
use Dustin\ImpEx\Resource\Exception\ResourceAlreadyExistsException;

class ResourceRegistry
{
    /**
     * @var array
     */
    private $factories = [];

    /**
     * @var array
     */
    private $resources = [];

    /**
     * @throws MultipleFactoryException
     */
    public function addFactory(ResourceFactoryInterface $factory): void
    {
        foreach ($factory->getResourceNames() as $name) {
            if ($this->hasFactory($name)) {
                throw new MultipleFactoryException($name, get_class($factory), get_class($this->getFactory($name)));
            }

            $this->factories[$name] = $factory;
        }
    }

    public function hasFactory(string $name): bool
    {
        return isset($this->factories[$name]);
    }

    public function getFactory(string $name): ?ResourceFactoryInterface
    {
        return isset($this->factories[$name]) ? $this->factories[$name] : null;
    }

    /**
     * @throws ResourceAlreadyExistsException
     */
    public function addResource(ResourceInterface $resource): void
    {
        $name = $resource->getName();

        if (isset($this->resources[$name]) || isset($this->factories[$name])) {
            throw new ResourceAlreadyExistsException(
                $name,
                get_class($resource),
                isset($this->resources[$name]) ? get_class($this->resources[$name]) : get_class($this->factories[$name])
            );
        }

        $this->resources[$name] = $resource;
    }

    /**
     * @param object $resource
     */
    public function createResource($resource, string $name, bool $obtainable): void
    {
        $this->addResource(new ResourceWrapper($resource, $name, $obtainable));
    }

    public function isAvailable(string $name): bool
    {
        return isset($this->resources[$name]) || isset($this->factories[$name]);
    }

    public function isObtainable(string $name): bool
    {
        return $this->obtainResource($name) !== null;
    }

    /**
     * @return mixed
     */
    public function getResource(string $name)
    {
        $this->ensureResourceExists($name);

        if (!isset($this->resources[$name])) {
            return null;
        }

        if ($this->resources[$name] instanceof ResourceWrapper) {
            return $this->resources[$name]->getResource();
        }

        return $this->resources[$name];
    }

    public function obtain(array $resources): ResourceContainer
    {
        return new ResourceContainer(
            array_filter($this->obtainList($resources))
        );
    }

    /**
     * @return object|null
     */
    protected function obtainResource(string $name)
    {
        $this->ensureResourceExists($name);

        if (!isset($this->resources[$name])) {
            return null;
        }

        /** @var object $resource */
        $resource = $this->resources[$name];

        if (!$resource->isObtainable()) {
            return null;
        }

        if ($resource instanceof ResourceWrapper) {
            return $resource->getResource();
        }

        return $resource;
    }

    protected function obtainList(array $resources): array
    {
        $obtainedResources = [];

        foreach ($resources as $resource) {
            $obtainedResources[$resource] = $this->obtainResource($resource);
        }

        return $obtainedResources;
    }

    private function ensureResourceExists(string $name): void
    {
        if (
            !isset($this->resources[$name]) &&
            isset($this->factories[$name])
        ) {
            $this->resources[$name] = $this->factories[$name]->createResource($name);
        }
    }
}
