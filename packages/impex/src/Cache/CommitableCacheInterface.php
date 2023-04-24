<?php

namespace Dustin\ImpEx\Cache;

interface CommitableCacheInterface extends CacheInterface
{
    public function commit();

    public function rollback();
}
