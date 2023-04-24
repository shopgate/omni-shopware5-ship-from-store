<?php

namespace Dustin\ImpEx\Cache;

use Dustin\ImpEx\Cache\KeyGenerator\KeyGeneratorInterface;

abstract class Cache implements CacheInterface
{
    use CacheTrait;

    /**
     * @var KeyGeneratorInterface
     */
    private $generator;

    public function __construct(
        KeyGeneratorInterface $generator
    ) {
        $this->generator = $generator;
    }

    public function getKeyGenerator(): KeyGeneratorInterface
    {
        return $this->generator;
    }
}
