<?php

namespace SgateShipFromStore\Framework\Sequence\Task;

use Dustin\ImpEx\Sequence\RecordHandling;
use Dustin\ImpEx\Sequence\Transferor;
use SgateShipFromStore\Framework\Task\TaskInterface;

class RecordHandlingTask implements TaskInterface
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

    public function execute()
    {
        $this->handler->handle($this->transferor);
    }
}
