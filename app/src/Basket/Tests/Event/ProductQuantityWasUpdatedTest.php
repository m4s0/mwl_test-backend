<?php

declare(strict_types=1);

namespace Basket\Tests\Event;

use Basket\Event\ProductQuantityWasUpdated;
use Basket\ValueObject\BasketId;
use Broadway\Serializer\Testing\SerializableEventTestCase;
use Money\Money;

class ProductQuantityWasUpdatedTest extends SerializableEventTestCase
{
    /**
     * @test
     */
    public function getters_of_event_work()
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

    protected function createEvent()
    {
        return new ProductQuantityWasUpdated(
            new BasketId('27da5b09-791d-4a26-8423-111dc552d145'),
            '1',
            Money::EUR('9999'),
            1
        );
    }
}
