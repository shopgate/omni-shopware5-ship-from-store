<?php

namespace SgateShipFromStore\Framework\Sequence\Task;

use Dustin\ImpEx\Sequence\RecordHandling;
use Dustin\ImpEx\Sequence\Transferor;

class RecordHandlingTask
{
    /**
     * @var Transferor
     */
    private $transferor;

    /**
     * @var RecordHandling
     */
    private $handler;

    public function __construct(
        Transferor $transferor,
        RecordHandling $handler
    ) {
        $this->transferor = $transferor;
        $this->handler = $handler;
    }

    public function execute(): void
    {
        $this->handler->handle($this->transferor);
    }
}
