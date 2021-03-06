<?php

declare(strict_types=1);

namespace Basket\Tests\CommandHandler;

use Basket\Command\AddProductToBasket;
use Basket\Command\Checkout;
use Basket\Command\PickUpBasket;
use Basket\Command\RemoveProductFromBasket;
use Basket\Command\UpdateProductQuantity;
use Basket\Event\BasketCheckedOut;
use Basket\Event\BasketWasPickedUp;
use Basket\Event\ProductQuantityWasUpdated;
use Basket\Event\ProductWasAddedToBasket;
use Basket\Event\ProductWasRemovedFromBasket;
use Basket\Exception\BasketAlreadyCheckedOutException;
use Basket\Exception\EmptyBasketException;
use Basket\Exception\ProductNotInBasketException;
use Basket\Handler\BasketCommandHandler;
use Basket\Repository\BasketRepository;
use Basket\ValueObject\BasketId;
use Broadway\CommandHandling\CommandHandler;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBus;
use Broadway\EventStore\EventStore;
use Money\Money;

class BasketCommandHandlerTest extends CommandHandlerScenarioTestCase
{
    /**
     * @test
     */
    public function it_picks_up_a_basket()
    {
        $basketId = new BasketId('81b56c94-0946-4b2a-864d-59f2132049f8');
        $this->scenario
            ->given([])
            ->when(new PickUpBasket($basketId))
            ->then([
                new BasketWasPickedUp($basketId),
            ]);
    }

    /**
     * @test
     */
    public function it_adds_a_product_to_a_basket()
    {
        $basketId = new BasketId('81b56c94-0946-4b2a-864d-59f2132049f8');

        $this->scenario
            ->withAggregateId((string) $basketId)
            ->given([new BasketWasPickedUp($basketId)])
            ->when(
                new AddProductToBasket(
                    $basketId,
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                )
            )
            ->then([
                new ProductWasAddedToBasket(
                    $basketId,
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                ),
            ]);
    }

    /**
     * @test
     */
    public function multiple_products_can_be_added_to_a_basket()
    {
        $basketId = new BasketId('81b56c94-0946-4b2a-864d-59f2132049f8');

        $this->scenario
            ->withAggregateId((string) $basketId)
            ->given([
                new BasketWasPickedUp($basketId),
                new ProductWasAddedToBasket(
                    $basketId,
                    '2',
                    'LCD Monitor',
                    Money::EUR('39990'),
                    1
                ),
            ])
            ->when(new AddProductToBasket(
                    $basketId,
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                )
            )
            ->then([
                new ProductWasAddedToBasket(
                    $basketId,
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                ),
            ]);
    }

    /**
     * @test
     */
    public function a_product_can_be_added_to_a_basket_multiple_times()
    {
        $basketId = new BasketId('81b56c94-0946-4b2a-864d-59f2132049f8');

        $this->scenario
            ->withAggregateId((string) $basketId)
            ->given([
                new BasketWasPickedUp($basketId),
                new ProductWasAddedToBasket(
                    $basketId,
                    '2',
                    'LCD Monitor',
                    Money::EUR('39990'),
                    1
                ),
            ])
            ->when(
                new AddProductToBasket(
                    $basketId,
                    '2',
                    'LCD Monitor',
                    Money::EUR('39990'),
                    1
                )
            )
            ->then([
                new ProductWasAddedToBasket(
                    $basketId,
                    '2',
                    'LCD Monitor',
                    Money::EUR('39990'),
                    1
                ),
            ]);
    }

    /**
     * @test
     */
    public function it_removes_a_product_that_was_added()
    {
        $basketId = new BasketId('81b56c94-0946-4b2a-864d-59f2132049f8');

        $this->scenario
            ->withAggregateId((string) $basketId)
            ->given([
                new BasketWasPickedUp($basketId),
                new ProductWasAddedToBasket(
                    $basketId,
                    '2',
                    'LCD Monitor',
                    Money::EUR('39990'),
                    1
                ),
            ])
            ->when(new RemoveProductFromBasket($basketId, '2', 'LCD Monitor'))
            ->then([
                new ProductWasRemovedFromBasket($basketId, '2', 'LCD Monitor'),
            ]);
    }

    /**
     * @test
     */
    public function it_throw_an_exception_when_removing_a_product_that_is_not_in_a_basket()
    {
        $this->expectException(ProductNotInBasketException::class);

        $basketId = new BasketId('81b56c94-0946-4b2a-864d-59f2132049f8');

        $this->scenario
            ->withAggregateId((string) $basketId)
            ->given([
                new BasketWasPickedUp($basketId),
            ])
            ->when(new RemoveProductFromBasket($basketId, '1', 'Smartphone'));
    }

    /**
     * @test
     */
    public function it_throw_an_exception_when_removing_a_product_that_already_has_been_removed()
    {
        $this->expectException(ProductNotInBasketException::class);

        $basketId = new BasketId('81b56c94-0946-4b2a-864d-59f2132049f8');

        $this->scenario
            ->withAggregateId((string) $basketId)
            ->given([
                new BasketWasPickedUp($basketId),
                new ProductWasAddedToBasket(
                    $basketId,
                    '2',
                    'LCD Monitor',
                    Money::EUR('39990'),
                    1
                ),
                new ProductWasAddedToBasket(
                    $basketId,
                    '2',
                    'LCD Monitor',
                    Money::EUR('39990'),
                    1
                ),
                new ProductWasRemovedFromBasket($basketId, '2', 'LCD Monitor'),
            ])
            ->when(new RemoveProductFromBasket($basketId, '2', 'LCD Monitor'));
    }

    /**
     * @test
     */
    public function it_updates_quantity_of_a_product_in_a_basket()
    {
        $basketId = new BasketId('81b56c94-0946-4b2a-864d-59f2132049f8');

        $this->scenario
            ->withAggregateId((string) $basketId)
            ->given([
                new BasketWasPickedUp($basketId),
                new ProductWasAddedToBasket(
                    $basketId,
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                ),
                new ProductWasAddedToBasket(
                    $basketId,
                    '2',
                    'LCD Monitor',
                    Money::EUR('39990'),
                    3
                ),
                new ProductWasAddedToBasket(
                    $basketId,
                    '2',
                    'LCD Monitor',
                    Money::EUR('39990'),
                    5
                ),
            ])
            ->when(
                new UpdateProductQuantity(
                $basketId,
                '2',
                Money::EUR('39990'),
                7
                )
            )
            ->then([
                new ProductQuantityWasUpdated(
                    $basketId,
                    '2',
                    Money::EUR('39990'),
                    7
                ),
            ]);
    }

    /**
     * @test
     */
    public function it_throw_an_exception_when_updating_a_product_that_is_not_in_a_basket()
    {
        $this->expectException(ProductNotInBasketException::class);

        $basketId = new BasketId('81b56c94-0946-4b2a-864d-59f2132049f8');

        $this->scenario
            ->withAggregateId((string) $basketId)
            ->given([
                new BasketWasPickedUp($basketId),
                new ProductWasAddedToBasket(
                    $basketId,
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                ),
                new RemoveProductFromBasket(
                    $basketId,
                    '2',
                    'LCD Monitor'
                ),
            ])
            ->when(
                new UpdateProductQuantity(
                    $basketId,
                    '2',
                    Money::EUR('39990'),
                    7
                )
            );
    }

    /**
     * @test
     */
    public function it_throw_an_exception_when_updating_a_product_and_quantity_is_not_valid()
    {
        $this->expectException(ProductNotInBasketException::class);

        $basketId = new BasketId('81b56c94-0946-4b2a-864d-59f2132049f8');

        $this->scenario
            ->withAggregateId((string) $basketId)
            ->given([
                new BasketWasPickedUp($basketId),
                new ProductWasAddedToBasket(
                    $basketId,
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                ),
            ])
            ->when(
                new UpdateProductQuantity(
                    $basketId,
                    '2',
                    Money::EUR('39990'),
                    -1
                )
            );
    }

    /**
     * @test
     */
    public function it_checks_out_a_basket()
    {
        $basketId = new BasketId('81b56c94-0946-4b2a-864d-59f2132049f8');
        $this->scenario
            ->withAggregateId((string) $basketId)
            ->given([
                new BasketWasPickedUp($basketId),
                new ProductWasAddedToBasket(
                    $basketId,
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                ),
                new ProductWasAddedToBasket(
                    $basketId,
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                ),
                new ProductWasAddedToBasket(
                    $basketId,
                    '2',
                    'LCD Monitor',
                    Money::EUR('39990'),
                    1
                ),
            ])
            ->when(new Checkout($basketId))
            ->then([
                new BasketCheckedOut($basketId),
            ]);
    }

    /**
     * @test
     */
    public function it_cannot_checks_out_an_empty_basket()
    {
        $this->expectException(EmptyBasketException::class);

        $basketId = new BasketId('81b56c94-0946-4b2a-864d-59f2132049f8');

        $this->scenario
            ->withAggregateId((string) $basketId)
            ->given([
                new BasketWasPickedUp($basketId),
            ])
            ->when(new Checkout($basketId));
    }

    /**
     * @test
     */
    public function it_cannot_checks_out_a_basket_that_has_been_emptied()
    {
        $this->expectException(EmptyBasketException::class);

        $basketId = new BasketId('81b56c94-0946-4b2a-864d-59f2132049f8');

        $this->scenario
            ->withAggregateId((string) $basketId)
            ->given([
                new BasketWasPickedUp($basketId),
                new ProductWasAddedToBasket(
                    $basketId,
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                ),
                new ProductWasRemovedFromBasket($basketId, '1', 'Smartphone'),
            ])
            ->when(new Checkout($basketId));
    }

    /**
     * @test
     */
    public function nothing_happens_when_checking_out_a_basket_for_a_second_time()
    {
        $this->expectException(BasketAlreadyCheckedOutException::class);

        $basketId = new BasketId('81b56c94-0946-4b2a-864d-59f2132049f8');

        $this->scenario
            ->withAggregateId((string) $basketId)
            ->given([
                new BasketWasPickedUp($basketId),
                new ProductWasAddedToBasket(
                    $basketId,
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                ),
                new BasketCheckedOut($basketId),
            ])
            ->when(new Checkout($basketId));
    }

    protected function createCommandHandler(EventStore $eventStore, EventBus $eventBus): CommandHandler
    {
        return new BasketCommandHandler(new BasketRepository($eventStore, $eventBus));
    }
}
