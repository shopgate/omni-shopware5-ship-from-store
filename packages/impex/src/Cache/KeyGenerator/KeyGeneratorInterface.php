<?php

namespace Dustin\ImpEx\Cache\KeyGenerator;

interface KeyGeneratorInterface
{
    const PREFIX = 'prefix';
    const KEY = 'key';

    /**
     * Generates the key for the cache prefix.
     */
    public function generatePrefixKey(string $prefix): string;

    /**
     * Generates the key for the cache item.
     */
    public function generateKey(string $key): string;

    /**
     * Glues prefix key and item key to create cache field.
     */
    public function glueKeys(string $generatedPrefix, string $generatedKey): string;

    /**
     * Splits glued keys to seperate prefix and item.
     * Return array should look like this:
     * [
     *  'prefix' => 'the prefix',
     *  'key' => 'the key'
     * ].
     */
    public function splitKeys(string $item): array;
}
