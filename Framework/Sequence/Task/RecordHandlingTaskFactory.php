<?php

namespace SgateShipFromStore\Framework\Sequence\Task;

use Dustin\ImpEx\Sequence\Registry\RecordHandlingRegistry;
use Dustin\ImpEx\Sequence\Registry\TransferorRegistry;
use Dustin\ImpEx\Sequence\Transferor;

class RecordHandlingTaskFactory
{
    /**
     * @var RecordHandlingRegistry
     */
    private $sequenceRegistry;

    /**
     * @var TransferorRegistry
     */
    private $transferorRegistry;

    public function __construct(
        RecordHandlingRegistry $sequenceRegistry,
        TransferorRegistry $transferorRegistry
    ) {
        $this->sequenceRegistry = $sequenceRegistry;
        $this->transferorRegistry = $transferorRegistry;
    }

    public function buildTask(string $sequenceName, ?Transferor $transferor = null): RecordHandlingTask
    {
        $transferor = $transferor ?? $this->transferorRegistry->getTransferor($sequenceName);
        $sequence = $this->sequenceRegistry->createRecordHandling($sequenceName);

        return new RecordHandlingTask($transferor, $sequence);
    }
}
