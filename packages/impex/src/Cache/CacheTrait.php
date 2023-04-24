<?php

namespace Dustin\ImpEx\Cache;

use Dustin\ImpEx\Encapsulation\EncapsulationTrait;

trait CacheTrait
{
    use EncapsulationTrait;

    public function addItem(string $prefix, string $key, $value): void
    {
        $field = $this->generateItemKey($prefix, $key);

        $this->set($field, $value);
    }

    public function getItem(string $prefix, string $key)
    {
        $field = $this->generateItemKey($prefix, $key);

        return $this->get($field);
    }

    public function hasItem(string $prefix, string $key): bool
    {
        $field = $this->generateItemKey($prefix, $key);

        return $this->has($field);
    }

    public function deleteItem(string $prefix, string $key): void
    {
        $field = $this->generateItemKey($prefix, $key);

        $this->unset($field);
    }

    public function clear(): void
    {
        foreach ($this->getFields() as $field) {
            $this->unset($field);
        }
    }

    protected function generateItemKey(string $prefix, string $key): string
    {
        $generator = $this->getKeyGenerator();

        return $generator->glueKeys(
            $generator->generatePrefixKey($prefix),
            $generator->generateKey($key)
        );
    }
}
