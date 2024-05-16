<?php

namespace SgateShipFromStore\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Shopware\Models\Customer\Customer;

class CustomerModelSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::postUpdate,
        ];
    }

    public function postUpdate(LifecycleEventArgs $arguments): void
    {
        $modelManager = $arguments->getEntityManager();
        $model = $arguments->getEntity();

        if (!$model instanceof Customer) {
            return;
        }

        $attribute = $model->getAttribute();

        if (!$attribute) {
            return;
        }

        $attribute->setSgateShipFromStoreCustomerExported(false);
        $modelManager->flush($attribute);
    }
}
