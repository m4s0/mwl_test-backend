<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\DropAndCreateEventStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EventStoreDropAndCreateCommand extends Command
{
    protected static $defaultName = 'app:event-store:drop-and-create';

    private DropAndCreateEventStore $dropAndCreateEventStore;

    public function __construct(DropAndCreateEventStore $dropAndCreateEventStore)
    {
        parent::__construct();
        $this->dropAndCreateEventStore = $dropAndCreateEventStore;
    }

    protected function configure()
    {
        $this->setDescription('Drop and Creates the event store schema');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->dropAndCreateEventStore->execute();

        $io->success('Event store schema created');

        return 0;
    }
}
