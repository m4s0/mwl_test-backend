<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\DropAndCreateReadModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReadModelDropAndCreateCommand extends Command
{
    protected static $defaultName = 'app:read-model:drop-and-create';

    private DropAndCreateReadModel $createReadModel;

    public function __construct(DropAndCreateReadModel $createReadModel)
    {
        parent::__construct();
        $this->createReadModel = $createReadModel;
    }

    protected function configure()
    {
        $this->setDescription('Drop and Creates the read model');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->createReadModel->execute();

        $io->success('Read model created');

        return 0;
    }
}
