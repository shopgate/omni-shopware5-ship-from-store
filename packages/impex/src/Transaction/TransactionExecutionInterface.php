<?php

namespace Dustin\ImpEx\Transaction;

interface TransactionExecutionInterface
{
    public const SUCCESS = 'success';

    public function executeTransaction(Transaction $transaction): TransactionResult;
}
