<?php

namespace Dustin\ImpEx\Cache\KeyGenerator;

class Md5 implements KeyGeneratorInterface
{
    protected string $separator;

    public function __construct(string $separator = '-')
    {
        if (empty($separator)) {
            throw new \InvalidArgumentException('Key separator cannot be empty!');
        }

        $this->separator = $separator;
    }

    public function generatePrefixKey(string $prefix): string
    {
        return md5($prefix);
    }

    public function generateKey(string $key): string
    {
        return md5($key);
    }

    public function glueKeys(string $generatedPrefix, string $generatedKey): string
    {
        return $generatedPrefix.$this->separator.$generatedKey;
    }

    public function splitKeys(string $item): array
    {
        $splitted = explode($this->separator, $item);

        if (count($splitted) == 2) {
            return [
                self::PREFIX => $splitted[0],
                self::KEY => $splitted[1],
            ];
        }

        return [
            self::PREFIX => $splitted[0],
            self::KEY => implode($this->separator, array_slice($splitted, 1)),
        ];
    }
}
