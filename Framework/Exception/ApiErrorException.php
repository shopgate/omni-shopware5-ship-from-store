<?php

namespace SgateShipFromStore\Framework\Exception;

use Dustin\Encapsulation\ArrayEncapsulation;
use Dustin\Encapsulation\Encapsulation;

class ApiErrorException extends \Exception
{
    private array $errors = [];

    public function __construct(string $scope, ArrayEncapsulation ...$errors)
    {
        $this->errors = $errors;

        $message = static::buildMessage($scope, ...$errors);
        parent::__construct($message);
    }

    public static function fromResult(string $scope, array $errors): self
    {
        return new static($scope, ...array_map(function (array $data) { return new Encapsulation($data); }, $errors));
    }

    protected static function buildMessage(string $scope, ArrayEncapsulation ...$errors): string
    {
        $message = sprintf("[%s] Caught %s errors:\n", $scope, count($errors));
        $message .= json_encode($errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
