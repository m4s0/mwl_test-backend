<?php

declare(strict_types=1);

namespace Domain\Basket\Repository;

use Broadway\EventHandling\EventBus;
use Broadway\EventSourcing\AggregateFactory\PublicConstructorAggregateFactory;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\EventStore;
use Domain\Basket\Aggregate\Basket;

class BasketRepository extends EventSourcingRepository
{
    public function __construct(
        EventStore $eventStore,
        EventBus $eventBus,
        array $eventStreamDecorators = []
    ) {
        parent::__construct(
            $eventStore,
            $eventBus,
            Basket::class,
            new PublicConstructorAggregateFactory(),
            $eventStreamDecorators
        );
    }
}
