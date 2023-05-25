<?php

namespace SgateShipFromStore\Framework\Exception;

class CancelRetryException extends \Exception
{
    /**
     * @var \Throwable
     */
    private $exception;

    public function __construct(\Throwable $exception)
    {
        $this->exception = $exception;

        $message = "Retry should be cancelled due to the following error:\n".$exception->getMessage();
        parent::__construct($message);
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }
}
