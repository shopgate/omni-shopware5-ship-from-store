<?php

namespace Dustin\ImpEx\Transaction;

use Dustin\ImpEx\Encapsulation\ReflectionEncapsulation;

class TransactionResult extends ReflectionEncapsulation
{
    protected bool $success = false;

    public function getSuccess(): bool
    {
        return $this->success;
    }
}
