<?php

namespace Dustin\ImpEx\Sequence;

use Dustin\ImpEx\Encapsulation\Container;

class Sequence extends AbstractSequence
{
    public function handle(Transferor $rootTransferor): void
    {
        $this->transferor = $rootTransferor;
        $count = count($this->handlers);
        $i = 0;

        /** @var RecordHandling $recordHandler */
        foreach ($this->handlers as $recordHandler) {
            ++$i;
            if ($recordHandler instanceof Transferor) {
                $recordHandler->handle($this->transferor);
                $this->setTransferor($recordHandler);

                continue;
            }

            if (
                !($this->transferor instanceof Container) &&
                $i < $count
            ) {
                $container = $this->accommodateRecords($this->transferor);
                $this->setTransferor($container);
            }

            $recordHandler->handle($this->transferor);
        }
    }

    protected function accommodateRecords(Transferor $transferor, Container $container = null): Container
    {
        if ($container === null) {
            $container = new Container();
        }

        $container->accommodate($transferor);

        return $container;
    }
}
