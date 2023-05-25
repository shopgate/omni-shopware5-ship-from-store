<?php

namespace SgateShipFromStore\Framework\Task;

use SgateShipFromStore\Framework\Exception\CancelRetryException;

abstract class Task implements TaskInterface
{
    abstract public function execute();

    public function retry(int $retries = 3)
    {
        if ($retries <= 0) {
            $retries = 1;
        }

        $count = 0;
        $exception = null;

        while ($count < $retries) {
            try {
                return $this->execute();
            } catch (CancelRetryException $exception) {
                throw $exception->getException();
            } catch (\Throwable $th) {
                ++$count;
                $exception = $th;
            }
        }

        throw $exception;
    }
}
