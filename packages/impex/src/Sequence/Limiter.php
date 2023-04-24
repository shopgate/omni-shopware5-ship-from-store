<?php

namespace Dustin\ImpEx\Sequence;

use Dustin\ImpEx\Encapsulation\Config;

class Limiter implements RecordHandling, Transferor
{
    protected int $limit = -1;

    protected ?Transferor $transferor = null;

    public function __construct(Config $config)
    {
        $this->limit = $this->getLimit($config);
    }

    public function handle(Transferor $transferor): void
    {
        $this->transferor = $transferor;
    }

    public function passRecords(): \Generator
    {
        if ($this->transferor === null) {
            return;
        }

        $fetched = 0;

        foreach ($this->transferor->passRecords() as $record) {
            ++$fetched;
            yield $record;

            if ($fetched === $this->limit) {
                break;
            }
        }
    }

    private function getLimit(Config $config): int
    {
        $limit = intval($config->get('limit'));

        return $limit > 0 ? $limit : -1;
    }
}
