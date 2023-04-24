<?php

namespace Dustin\ImpEx\Cache\Exception;

class InvalidCacheItemException extends \Exception
{
    /**
     * @var string
     */
    private $cacheKey;

    /**
     * @var \Throwable|null
     */
    private $error;

    public function __construct(
        string $message,
        string $cacheKey,
        \Throwable $th = null
    ) {
        $this->cacheKey = $cacheKey;
        $this->$error = $th;

        parent::__construct($message);
    }

    public function getError(): ?\Throwable
    {
        return $this->error;
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }
}
