<?php

declare(strict_types=1);

namespace Domain\Basket\Tests\Event;

use Broadway\Serializer\Testing\SerializableEventTestCase;
use Domain\Basket\Event\ProductWasRemovedFromBasket;
use Domain\Basket\ValueObject\BasketId;

class ProductWasRemovedFromBasketTest extends SerializableEventTestCase
{
    /**
     * @test
     */
    public function getters_of_event_work(): void
    {
        $basketId = new BasketId('27da5b09-791d-4a26-8423-111dc552d145');
        $productId = '1';
        $productName = 'LCD Monitor';

        $event = new ProductWasRemovedFromBasket($basketId, $productId, $productName);

        $this->assertEquals($basketId, $event->getBasketId());
        $this->assertEquals($productId, $event->getProductId());
        $this->assertEquals($productName, $event->getProductName());
    }

    protected function createEvent(): ProductWasRemovedFromBasket
    {
        return new ProductWasRemovedFromBasket(
            new BasketId('27da5b09-791d-4a26-8423-111dc552d145'),
            '1',
            'LCD Monitor'
        );
    }
}
