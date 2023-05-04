<?php

namespace SgateShipFromStore\Framework\Sequence\Task;

use Dustin\ImpEx\Resource\ResourceRegistry;
use Dustin\ImpEx\Sequence\Registry\RecordHandlingRegistry;

class RecordHandlingTaskFactory
{
    /**
     * @var RecordHandlingRegistry
     */
    private $sequenceRegistry;

    /**
     * @var ResourceRegistry
     */
    private $resourceRegistry;

    public function __construct(
        RecordHandlingRegistry $sequenceRegistry,
        ResourceRegistry $resourceRegistry
    ) {
        $this->sequenceRegistry = $sequenceRegistry;
        $this->resourceRegistry = $resourceRegistry;
    }

    public function buildTask(string $sequenceName): RecordHandlingTask
    {
        $transferorName = 'transferor.'.$sequenceName;
        $transferor = $this->resourceRegistry->getResource($transferorName);

        if ($transferor === null) {
            throw new \Exception(\sprintf("A transferor for sequence %s was not found! It's name must be %s.", $sequenceName, $transferorName));
        }

        $sequence = $this->sequenceRegistry->createRecordHandling($sequenceName);

        return new RecordHandlingTask($transferor, $sequence);
    }
}
