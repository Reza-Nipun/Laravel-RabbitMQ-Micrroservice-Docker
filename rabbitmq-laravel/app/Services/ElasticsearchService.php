<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts(config('services.elasticsearch.hosts'))
            ->build();
    }

    public function search(string $index, array $query): array
    {
        return $this->client->search([
            'index' => $index,
            'body' => [
                'query' => [
                    'match' => $query,
                ],
            ],
        ])->asArray(); // Converts to array
    }

    public function index(string $index, array $data): array
    {
        return $this->client->index([
            'index' => $index,
            'id' => $data['id'], // assumes model has an ID
            'body' => $data,
        ])->asArray(); // Converts to array
    }

    public function delete(string $index, int $id): array
    {
        return $this->client->delete([
            'index' => $index,
            'id' => $id,
        ])->asArray(); // Converts to array
    }
}
