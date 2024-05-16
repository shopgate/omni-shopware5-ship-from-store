<?php

namespace SgateShipFromStore\Framework;

use Monolog\Logger as BaseLogger;
use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function emergency($message, array $context = [])
    {
        $this->log(BaseLogger::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = [])
    {
        $this->log(BaseLogger::ALERT, $message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->log(BaseLogger::CRITICAL, $message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->log(BaseLogger::ERROR, $message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->log(BaseLogger::WARNING, $message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->log(BaseLogger::NOTICE, $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->log(BaseLogger::INFO, $message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->log(BaseLogger::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = [])
    {
        $message = str_replace(['{', '}'], ['[', ']'], (string) $message);

        $this->logger->log($level, $message, $context);
    }
}
