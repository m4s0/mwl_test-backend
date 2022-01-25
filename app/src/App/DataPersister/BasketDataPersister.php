<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\BasketOutput;
use App\Dto\CreateBasket;
use Broadway\CommandHandling\CommandBus;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use Domain\Basket\Command\PickUpBasket;
use Domain\Basket\ValueObject\BasketId;
use LogicException;

class BasketDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private CommandBus $commandBus,
        private UuidGeneratorInterface $uuidGenerator
    )
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof CreateBasket;
    }

    public function persist($data, array $context = [])
    {
        $basketId = new BasketId($this->uuidGenerator->generate());
        $command = new PickUpBasket($basketId);

        $this->commandBus->dispatch($command);
        $basketOutput = new BasketOutput();
        $basketOutput->basketId = (string)$basketId;

        return $basketOutput;
    }

    public function remove($data, array $context = []): void
    {
        throw new LogicException('Should never been called. Not relevant in a ES system.');
    }
}