<?php

declare(strict_types=1);

namespace Basket\Repository;

use Basket\Aggregate\Basket;
use Broadway\EventHandling\EventBus;
use Broadway\EventSourcing\AggregateFactory\PublicConstructorAggregateFactory;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\EventStore;

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
