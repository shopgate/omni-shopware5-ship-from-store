<?php

namespace SgateShipFromStore\Framework\Sequence\Command;

use SgateShipFromStore\Framework\Sequence\Task\RecordHandlingTaskFactory;
use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SequenceStartCommand extends ShopwareCommand
{
    /**
     * @var RecordHandlingTaskFactory
     */
    private $factory;

    public function __construct(RecordHandlingTaskFactory $factory)
    {
        parent::__construct();

        $this->factory = $factory;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sequenceName = $input->getArgument('sequence');
        $task = $this->factory->buildTask($sequenceName);

        $task->execute();

        return 0;
    }

    protected function configure(): void
    {
        $this->addArgument('sequence', InputArgument::REQUIRED);
    }
}
