<?php

declare(strict_types=1);

namespace Domain\Basket\Tests\Event;

use Broadway\Serializer\Testing\SerializableEventTestCase;
use Domain\Basket\Event\BasketWasPickedUp;
use Domain\Basket\ValueObject\BasketId;

class BasketWasPickedUpTest extends SerializableEventTestCase
{
    /**
     * @test
     */
    public function getters_of_event_work(): void
    {
        $basketId = new BasketId('27da5b09-791d-4a26-8423-111dc552d145');
        $event = new BasketWasPickedUp($basketId);

        $this->assertEquals($basketId, $event->getBasketId());
    }

    protected function createEvent(): BasketWasPickedUp
    {
        return new BasketWasPickedUp(new BasketId('27da5b09-791d-4a26-8423-111dc552d145'));
    }
}
