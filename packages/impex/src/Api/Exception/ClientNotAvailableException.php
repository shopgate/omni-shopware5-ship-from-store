<?php

namespace Dustin\ImpEx\Api\Exception;

class ClientNotAvailableException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Api client could not be created!');
    }
}
