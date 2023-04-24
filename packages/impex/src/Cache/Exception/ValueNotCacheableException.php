<?php

namespace Dustin\ImpEx\Cache\Exception;

class ValueNotCacheableException extends \Exception
{
    /**
     * @var string
     */
    private $cacheKey;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var \Throwable|null
     */
    private $error;

    public function __construct(
        string $message,
        string $cacheKey,
        $value = null,
        \Throwable $error = null
    ) {
        $this->cacheKey = $cacheKey;
        $this->value = $value;
        $this->error = $error;

        parent::__construct($message);
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getError(): ?\Throwable
    {
        return $this->error;
    }
}
