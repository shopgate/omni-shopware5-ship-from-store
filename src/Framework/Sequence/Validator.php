<?php

namespace SgateShipFromStore\Framework\Sequence;

use Dustin\ImpEx\Sequence\Filter;
use Psr\Log\LoggerInterface;
use SgateShipFromStore\Framework\ValidatableInterface;
use Shopware\Components\Api\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator extends Filter
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ValidatorInterface $validator,
        LoggerInterface $logger
    ) {
        $this->validator = $validator;
        $this->logger = $logger;
    }

    public function filter($record): bool
    {
        try {
            $this->validate($record);
        } catch (ValidationException $e) {
            $this->logger->error($e->getViolations());

            return false;
        }

        return true;
    }

    public function validate(ValidatableInterface $record): void
    {
        $context = $this->validator->startContext();

        foreach ($record->getConstraints() as $property => $constraints) {
            $context->atPath($property)->validate($record->get($property), $constraints);
        }

        $violations = $context->getViolations();

        if (count($violations) > 0) {
            throw new ValidationException($violations);
        }
    }
}
