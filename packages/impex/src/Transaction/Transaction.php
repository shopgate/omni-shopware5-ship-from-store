<?php

namespace Dustin\ImpEx\Transaction;

use Dustin\ImpEx\Encapsulation\Encapsulation;

class Transaction extends Encapsulation
{
    /**
     * @param Action $value
     */
    public function set(string $field, $value): void
    {
        $this->addAction($value, $field);
    }

    public function addAction(Action $action, string $name): void
    {
        parent::set($name, $action);
    }
}
