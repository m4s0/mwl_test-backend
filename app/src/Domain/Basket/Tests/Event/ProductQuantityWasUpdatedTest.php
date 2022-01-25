<?php

declare(strict_types=1);

namespace Domain\Basket\Tests\Event;

use Broadway\Serializer\Testing\SerializableEventTestCase;
use Domain\Basket\Event\ProductQuantityWasUpdated;
use Domain\Basket\ValueObject\BasketId;
use Money\Money;

class ProductQuantityWasUpdatedTest extends SerializableEventTestCase
{
    /**
     * @test
     */
    public function getters_of_event_work(): void
    {
        $basketId = new BasketId('27da5b09-791d-4a26-8423-111dc552d145');
        $productId = '1';
        $productPrice = Money::EUR('9999');
        $quantity = 1;

        $event = new ProductQuantityWasUpdated(
            $basketId,
            $productId,
            $productPrice,
            $quantity
        );

        $this->assertEquals($basketId, $event->getBasketId());
    }

    protected function createEvent(): ProductQuantityWasUpdated
    {
        return new ProductQuantityWasUpdated(
            new BasketId('27da5b09-791d-4a26-8423-111dc552d145'),
            '1',
            Money::EUR('9999'),
            1
        );
    }
}
