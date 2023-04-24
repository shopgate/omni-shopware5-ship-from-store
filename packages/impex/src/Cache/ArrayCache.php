<?php

namespace Dustin\ImpEx\Cache;

use Dustin\ImpEx\Cache\KeyGenerator\KeyGeneratorInterface;
use Dustin\ImpEx\Encapsulation\Encapsulation;
use Dustin\ImpEx\Utilize\Cache\KeyGenerator\Md5;

/**
 * Temporary cache to store values in an array.
 */
class ArrayCache extends Encapsulation implements CacheInterface
{
    use CacheTrait;

    /**
     * @var KeyGeneratorInterface
     */
    protected $generator;

    public function __construct(array $data = [])
    {
        $this->generator = new Md5();
        parent::__construct($data);
    }

    public function getKeyGenerator(): KeyGeneratorInterface
    {
        return $this->generator;
    }
}
