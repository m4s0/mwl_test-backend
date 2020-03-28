<?php

declare(strict_types=1);

namespace Basket\Repository;

use Assert\Assertion;
use Broadway\ReadModel\Identifiable;
use Broadway\ReadModel\Repository;
use Broadway\Serializer\Serializer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use JsonException;

class DBALRepository implements Repository
{
    private Connection $connection;
    private Serializer $serializer;
    private string     $tableName;
    private string     $class;

    public function __construct(
        Connection $connection,
        Serializer $serializer,
        string $tableName,
        string $class
    ) {
        $this->connection = $connection;
        $this->serializer = $serializer;
        $this->tableName = $tableName;
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Identifiable $readModel): void
    {
        Assertion::isInstanceOf($readModel, $this->class);

        $payload = json_encode($this->serializer->serialize($readModel), JSON_THROW_ON_ERROR, 512);
        $statement = $this->connection->prepare(
            sprintf(
                "INSERT INTO %s VALUES('%s', '%s') ON DUPLICATE KEY UPDATE products = '%s'",
                $this->tableName,
                $readModel->getId(),
                $payload,
                $payload,
            )
        );

        $statement->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id): ?Identifiable
    {
        $row = $this->connection->fetchAssoc(sprintf('SELECT * FROM %s WHERE uuid = ?', $this->tableName), [$id]);
        if (false === $row) {
            return null;
        }
        try {
            $object = json_decode($row['products'], true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $jsonException) {
            return null;
        }

        return $this->serializer->deserialize($object);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $fields): array
    {
        if (empty($fields)) {
            return [];
        }

        return array_values(array_filter($this->findAll(), function (Identifiable $readModel) use ($fields) {
            return $fields === array_intersect_assoc($this->serializer->serialize($readModel)['products'], $fields);
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $rows = $this->connection->fetchAll(sprintf('SELECT * FROM %s', $this->tableName));

        return array_map(function (array $row) {
            try {
                $serializedObject = json_decode($row['products'], true, 512, JSON_THROW_ON_ERROR);

                return $this->serializer->deserialize($serializedObject);
            } catch (JsonException $jsonException) {
                return null;
            }
        }, $rows);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id): void
    {
        $this->connection->executeUpdate(sprintf('DELETE FROM %s WHERE uuid = ?', $this->tableName), [$id]);
    }

    public function configureSchema(Schema $schema): ?Table
    {
        if ($schema->hasTable($this->tableName)) {
            return null;
        }

        return $this->configureTable($schema);
    }

    public function configureTable(Schema $schema): Table
    {
        $table = $schema->createTable($this->tableName);
        $table->addColumn('uuid', Types::GUID, ['length' => 36]);
        $table->addColumn('products', Types::JSON);
        $table->setPrimaryKey(['uuid']);

        return $table;
    }
}
