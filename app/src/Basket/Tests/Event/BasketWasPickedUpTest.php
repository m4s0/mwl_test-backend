<?php

declare(strict_types=1);

namespace Basket\Tests\Event;

use Basket\Event\BasketWasPickedUp;
use Basket\ValueObject\BasketId;
use Broadway\Serializer\Testing\SerializableEventTestCase;

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
