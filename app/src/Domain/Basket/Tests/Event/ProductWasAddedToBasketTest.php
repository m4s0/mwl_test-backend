<?php

declare(strict_types=1);

namespace Domain\Basket\Tests\Event;

use Broadway\Serializer\Testing\SerializableEventTestCase;
use Domain\Basket\Event\ProductWasAddedToBasket;
use Domain\Basket\ValueObject\BasketId;
use Money\Money;

class ProductWasAddedToBasketTest extends SerializableEventTestCase
{
    /**
     * @test
     */
    public function getters_of_event_work(): void
    {
        $basketId = new BasketId('27da5b09-791d-4a26-8423-111dc552d145');
        $productId = '1';
        $productName = 'Smartphone';
        $productPrice = Money::EUR('9999');
        $quantity = 1;
        $event = new ProductWasAddedToBasket($basketId, $productId, $productName, $productPrice, $quantity);

        $this->assertEquals($basketId, $event->getBasketId());
        $this->assertEquals($productId, $event->getProductId());
        $this->assertEquals($productName, $event->getProductName());
        $this->assertEquals($productPrice, $event->getProductPrice());
        $this->assertEquals($quantity, $event->getQuantity());
    }

    protected function createEvent(): ProductWasAddedToBasket
    {
        return new ProductWasAddedToBasket(
            new BasketId('27da5b09-791d-4a26-8423-111dc552d145'),
            '1',
            'Smartphone',
            Money::EUR('9999'),
            1
        );
    }
}
