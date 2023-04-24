<?php

namespace Dustin\ImpEx\Transaction;

use Dustin\ImpEx\Encapsulation\Encapsulation;
use Dustin\ImpEx\Transaction\Exception\DuplicateTransactionException;

class TransactionCarrier extends Encapsulation
{
    /**
     * @param Transaction $transaction
     */
    public function set(string $name, $transaction): void
    {
        $this->addTransaction($transaction, $name);
    }

    /**
     * @throws DuplicateTransactionException
     */
    public function addTransaction(Transaction $transaction, string $name): void
    {
        if ($this->has($name)) {
            throw new DuplicateTransactionException($name);
        }

        parent::set($name, $transaction);
    }

    public function emerge(string $name): Transaction
    {
        if (!$this->has($name)) {
            $this->set($name, new Transaction());
        }

        return $this->get($name);
    }

    public function pass(array $list = []): \Generator
    {
        $transactions = [];

        if (empty($list)) {
            $transactions = $this->normalize();
        } else {
            $transactions = array_filter($this->getList($list), function ($transaction) {
                return $transaction !== null;
            });
        }

        foreach ($transactions as $name => $transaction) {
            yield $transaction;

            $this->unset($name);
        }
    }

    public function pick(string $name): ?Transaction
    {
        if (!$this->has($name)) {
            return null;
        }

        $transaction = $this->get($name);
        $this->unset($name);

        return $transaction;
    }
}
