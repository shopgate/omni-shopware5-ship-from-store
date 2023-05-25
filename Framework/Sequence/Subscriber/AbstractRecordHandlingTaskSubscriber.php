<?php

namespace SgateShipFromStore\Framework\Sequence\Subscriber;

use Enlight\Event\SubscriberInterface;
use SgateShipFromStore\Framework\Sequence\Task\RecordHandlingTaskFactory;

abstract class AbstractRecordHandlingTaskSubscriber implements SubscriberInterface
{
    /**
     * @var RecordHandlingTaskFactory
     */
    private $factory;

    public function __construct(RecordHandlingTaskFactory $factory)
    {
        $this->factory = $factory;
    }

    abstract public static function getEventName(): string;

    abstract public function getSequence(): string;

    public static function getSubscribedEvents()
    {
        return [
            static::getEventName() => 'runTask',
        ];
    }

    public function runTask(\Shopware_Components_Cron_CronJob $event): void
    {
        $this->factory->buildTask($this->getSequence())->execute();
    }
}
