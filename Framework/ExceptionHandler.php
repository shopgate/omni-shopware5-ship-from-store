<?php

namespace SgateShipFromStore\Framework;

use Dustin\Encapsulation\ArrayEncapsulation;
use Psr\Log\LoggerInterface;
use SgateShipFromStore\Framework\Exception\ApiErrorException;
use Shopware\Components\Validator\EmailValidator;

class ExceptionHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ArrayEncapsulation
     */
    private $config;

    /**
     * @var EmailValidator
     */
    private $emailValidator;

    /**
     * @var Shopware_Components_Config
     */
    private $shopwareConfig;

    public function __construct(
        LoggerInterface $logger,
        ArrayEncapsulation $config,
        EmailValidator $emailValidator
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->emailValidator = $emailValidator;
        $this->shopwareConfig = Shopware()->Config();
    }

    public function handle(\Throwable $exception, int $shopId): void
    {
        if ($exception instanceof ApiErrorException) {
            foreach ($exception->getErrors() as $error) {
                $this->logger->error(json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        } else {
            $this->logger->error($exception->getMessage()."\n".$exception->getTraceAsString());
        }

        $this->sendErrorMail($exception, $shopId);
    }

    public function sendErrorMail(\Throwable $exception, int $shopId): void
    {
        $recipientMails = $this->getRecipientMails($shopId);

        if (empty($recipientMails)) {
            return;
        }

        $mail = clone Shopware()->Container()->get('mail');

        $mail->setFrom($this->shopwareConfig->get('mail'), 'Shopgate ShipFromStore');
        $mail->clearSubject();
        $mail->setSubject('Fehler bei Ship-from-Store');
        $mail->clearRecipients();

        foreach ($recipientMails as $recipient) {
            $mail->addTo($recipient);
        }

        $mail->setBodyText($exception->getMessage()."\n".$exception->getTraceAsString());
        $mail->send();
    }

    private function getRecipientMails(int $shopId): array
    {
        $config = $this->config->get($shopId);

        $mails = trim((string) $config->get('errorMailAddresses'), ',');
        $mails = explode(',', $mails);
        $mails = array_map(function ($mail) {
            return trim((string) $mail);
        }, $mails);

        $mails = array_filter($mails, function ($mail) {
            if (empty($mail)) {
                return false;
            }

            return $this->emailValidator->isValid($mail);
        });

        return $mails;
    }
}
