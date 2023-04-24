<?php

namespace Dustin\ImpEx\Cache;

use Dustin\ImpEx\Cache\KeyGenerator\KeyGeneratorInterface;
use Dustin\ImpEx\Encapsulation\Encapsulated;

interface CacheInterface extends Encapsulated
{
    /**
     * @return mixed
     */
    public function getItem(string $prefix, string $key);

    public function addItem(string $prefix, string $key, $item): void;

    public function hasItem(string $prefix, string $key): bool;

    public function deleteItem(string $prefix, string $key): void;

    public function getKeyGenerator(): KeyGeneratorInterface;
}
