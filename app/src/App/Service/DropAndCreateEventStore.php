<?php

declare(strict_types=1);

namespace App\Service;

use Broadway\EventStore\Dbal\DBALEventStore;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

class DropAndCreateEventStore
{
    private Connection $connection;
    private DBALEventStore $eventStore;

    public function __construct(Connection $connection, DBALEventStore $eventStore)
    {
        $this->connection = $connection;
        $this->eventStore = $eventStore;
    }

    public function execute(): void
    {
        $schemaManager = $this->connection->getSchemaManager();
        $table = $this->eventStore->configureTable(new Schema());

        if ($schemaManager->tablesExist([$table->getName()])) {
            $schemaManager->dropTable($table->getName());
        }

        $table = $this->eventStore->configureSchema($schemaManager->createSchema());
        $schemaManager->createTable($table);
    }
}
