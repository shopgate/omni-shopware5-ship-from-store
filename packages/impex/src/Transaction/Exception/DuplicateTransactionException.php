<?php

namespace Dustin\ImpEx\Transaction\Exception;

class DuplicateTransactionException extends \Exception
{
    public function __construct(string $name)
    {
        parent::__construct(\sprintf("A transaction with name '%s' is already in use! Choose another name or remove existing transaction!", $name));
    }
}
