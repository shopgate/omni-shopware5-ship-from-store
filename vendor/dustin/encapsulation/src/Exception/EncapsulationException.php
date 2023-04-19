<?php

namespace Dustin\Encapsulation\Exception;

use Dustin\Encapsulation\EncapsulationInterface;

class EncapsulationException extends \Exception
{
    /**
     * @var EncapsulationInterface
     */
    private $encapsulation;

    public function __construct(EncapsulationInterface $encapsulation, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $this->encapsulation = $encapsulation;

        parent::__construct($message, $code, $previous);
    }

    public function getEncapsulation(): EncapsulationInterface
    {
        return $this->encapsulation;
    }
}
