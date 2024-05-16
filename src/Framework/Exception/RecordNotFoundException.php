<?php

namespace SgateShipFromStore\Framework\Exception;

use Dustin\Encapsulation\EncapsulationInterface;
use Dustin\ImpEx\Util\Type;

class RecordNotFoundException extends \Exception
{
    /**
     * @var mixed
     */
    private $identifier;

    public function __construct($identifier)
    {
        $this->identifier = $identifier;

        parent::__construct(sprintf("Record for identifier '%s' was not found.", static::identifierToString($identifier)));
    }

    private static function identifierToString($identifier): string
    {
        if (is_scalar($identifier) || $identifier === null) {
            return (string) $identifier;
        }

        if (Type::is($identifier, EncapsulationInterface::class)) {
            return json_encode($identifier);
        }

        return serialize($identifier);
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }
}
