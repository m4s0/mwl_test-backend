<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Broadway\ReadModel\Repository;
use Domain\Basket\ReadModel\BasketReadModel;
use Domain\Basket\ValueObject\BasketId;

final class BasketItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(private Repository $repository)
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return BasketReadModel::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?BasketReadModel
    {
        return $this->repository->find(new BasketId($id));
    }
}