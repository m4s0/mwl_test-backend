<?php

declare(strict_types=1);

namespace App\Service;

use Elasticsearch\Client;

class DropAndCreateReadModel
{
    private Client $client;
    private array  $indexes;

    public function __construct(Client $client, array $indexes)
    {
        $this->client = $client;
        $this->indexes = $indexes;
    }

    public function execute(): void
    {
        foreach ($this->indexes as $index) {
            $this->dropAndCreate($index);
        }
    }

    protected function dropAndCreate(string $index): void
    {
        $params = [
            'index' => $index,
        ];

        if ($this->client->indices()->exists($params)) {
            $this->client->indices()->delete($params);
        }

        $this->client->indices()->create($params);
    }
}
