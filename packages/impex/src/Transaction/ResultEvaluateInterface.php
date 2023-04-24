<?php

namespace Dustin\ImpEx\Transaction;

interface ResultEvaluateInterface
{
    public function evaluate(TransactionResult $result, Transaction $transaction, TransactionExecutionInterface $executer): void;
}
