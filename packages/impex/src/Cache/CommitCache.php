<?php

namespace Dustin\ImpEx\Cache;

use Dustin\ImpEx\Cache\KeyGenerator\KeyGeneratorInterface;

class CommitCache implements CommitableCacheInterface
{
    use CacheTrait;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var CacheInterface
     */
    private $innerCache;

    public function __construct(CacheInterface $innerCache)
    {
        $this->innerCache = $innerCache;
    }

    public function getKeyGenerator(): KeyGeneratorInterface
    {
        return $this->innerCache->getKeyGenerator();
    }

    public function set(string $field, $value): void
    {
        $this->data[$field] = $value;
    }

    public function get(string $field)
    {
        if (array_key_exists($field, $this->data)) {
            return $this->data[$field];
        }

        return $this->innerCache->get($field);
    }

    public function has(string $field): bool
    {
        if (array_key_exists($field, $this->data)) {
            return true;
        }

        return $this->innerCache->has($field);
    }

    public function unset(string $field): void
    {
        unset($this->data[$field]);

        $this->innerCache->unset($field);
    }

    public function getFields(): array
    {
        $fields = array_merge(
            array_keys($this->data),
            $this->innerCache->getFields()
        );

        return array_unique($fields);
    }

    public function commit(): void
    {
        foreach ($this->data as $key => $value) {
            $this->innerCache->set($key, $value);
        }

        $this->rollback();
    }

    public function rollback(): void
    {
        $this->data = [];
    }
}
