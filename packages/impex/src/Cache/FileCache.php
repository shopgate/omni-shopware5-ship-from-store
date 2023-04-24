<?php

namespace Dustin\ImpEx\Cache;

use Dustin\ImpEx\Cache\Exception\InvalidCacheItemException;
use Dustin\ImpEx\Cache\Exception\ValueNotCacheableException;
use Dustin\ImpEx\Cache\KeyGenerator\KeyGeneratorInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Serializer\SerializerInterface;

class FileCache extends Cache
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $encodingFormat;

    public function __construct(
        KeyGeneratorInterface $keyGenerator,
        FilesystemInterface $filesystem,
        SerializerInterface $serializer,
        string $encodingFormat
    ) {
        $this->filesystem = $filesystem;
        $this->serializer = $serializer;
        $this->encodingFormat = $encodingFormat;

        parent::__construct($keyGenerator);
    }

    /**
     * @throws ValueNotCacheableException
     */
    public function set(string $field, $value): void
    {
        $data = ['_value' => $value];

        if (is_object($value)) {
            $data['_class'] = get_class($value);
        }

        try {
            $serializedValue = $this->serializer->serialize($data, $this->encodingFormat);
        } catch (\Throwable $t) {
            throw new ValueNotCacheableException(
                sprintf('Value cannot be added to cache. %s', $t->getMessage()),
                $field, $value, $t
            );
        }

        $this->filesystem->put($field, $serializedValue);
    }

    /**
     * @throws InvalidCacheItemException
     */
    public function get(string $field)
    {
        if (!$this->has($field)) {
            return null;
        }

        try {
            $content = $this->filesystem->read($field);
            $data = $this->serializer->decode($content, $this->encodingFormat);
        } catch (\Throwable $th) {
            throw new InvalidCacheItemException(
                sprintf('An error occured while deserializing value for cache item %s. %s', $field, $th->getMessage()),
                $field,
                $th
            );
        }

        if (!isset($data['_value'])) {
            throw new InvalidCacheItemException(
                sprintf('Could not find value in cache item %s', $field),
                $field
            );
        }

        if (isset($data['_class'])) {
            try {
                return $this->serializer->denormalize(
                    $data['_value'],
                    (string) $data['_class']
                );
            } catch (\Throwable $th) {
                throw new InvalidCacheItemException(
                    sprintf('An error occured while deserializing value for cache item %s. %s', $field, $th->getMessage()),
                    $field,
                    $th
                );
            }
        }

        return $data['_value'];
    }

    public function has(string $field): bool
    {
        if (!$this->filesystem->has($field)) {
            return false;
        }

        $metaData = $this->filesystem->getMetadata($field);

        return isset($metaData['type']) && \strtolower($metaData['type']) == 'file';
    }

    public function unset(string $file): void
    {
        if ($this->has($file)) {
            $this->filesystem->delete($file);
        }
    }

    public function getFields(): array
    {
        $contents = $this->filesystem->listContents('.', true);

        $contents = array_filter($contents, function ($fileOrDir) {
            return isset($fileOrDir['type']) && \strtolower($fileOrDir['type']) == 'file';
        });

        return array_column($contents, 'path');
    }
}
