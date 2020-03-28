<?php

declare(strict_types=1);

namespace Basket\Tests\Projector;

use Basket\Event\BasketCheckedOut;
use Basket\Event\BasketWasPickedUp;
use Basket\Event\ProductQuantityWasUpdated;
use Basket\Event\ProductWasAddedToBasket;
use Basket\Event\ProductWasRemovedFromBasket;
use Basket\Projector\BasketProjector;
use Basket\ReadModel\BasketReadModel;
use Basket\ValueObject\BasketId;
use Broadway\ReadModel\InMemory\InMemoryRepository;
use Broadway\ReadModel\Projector;
use Broadway\ReadModel\Testing\ProjectorScenarioTestCase;
use Money\Money;
use PHPUnit\Framework\Assert;

class BasketProjectorTest extends ProjectorScenarioTestCase
{
    /**
     * @test
     */
    public function it_picks_up_a_basket()
    {
        $expectedBasketReadModel = new BasketReadModel('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858');
        Assert::assertFalse($expectedBasketReadModel->hasBeenCheckedOut());
        Assert::assertEquals([], $expectedBasketReadModel->getProducts());
        Assert::assertEquals('€0.00', $expectedBasketReadModel->getTotal());

        $this->scenario
            ->given([])
            ->when(new BasketWasPickedUp(new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858')))
            ->then([$expectedBasketReadModel]);
    }

    /**
     * @test
     */
    public function it_adds_product_to_basket()
    {
        $expectedBasketReadModel = new BasketReadModel('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858');
        $expectedBasketReadModel->addProduct(
            new ProductWasAddedToBasket(
                new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                '1',
                'Smartphone',
                Money::EUR('9999'),
                1
            )
        );
        Assert::assertFalse($expectedBasketReadModel->hasBeenCheckedOut());
        Assert::assertEquals([
            [
                'productId' => '1',
                'productName' => 'Smartphone',
                'productPrice' => [
                    'amount' => '9999',
                    'currency' => 'EUR',
                ],
                'quantity' => 1,
            ],
        ], $expectedBasketReadModel->getProducts());
        Assert::assertEquals('€99.99', $expectedBasketReadModel->getTotal());

        $this->scenario
            ->given([
                new BasketWasPickedUp(new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858')),
            ])
            ->when(
                new ProductWasAddedToBasket(
                    new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                )
            )
            ->then([$expectedBasketReadModel]);
    }

    /**
     * @test
     */
    public function it_removes_product_from_basket()
    {
        $expectedBasketReadModel = new BasketReadModel('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858');
        $expectedBasketReadModel->addProduct(
            new ProductWasAddedToBasket(
                new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                '1',
                'Smartphone',
                Money::EUR('9999'),
                1
            )
        );
        $expectedBasketReadModel->addProduct(
            new ProductWasAddedToBasket(
                new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                '2',
                'LCD Monitor',
                Money::EUR('49990'),
                1
            )
        );
        $expectedBasketReadModel->removeProduct(
            new ProductWasRemovedFromBasket(
                new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                '1',
                'Smartphone'
            )
        );
        Assert::assertFalse($expectedBasketReadModel->hasBeenCheckedOut());
        Assert::assertEquals([
            [
                'productId' => '2',
                'productName' => 'LCD Monitor',
                'productPrice' => [
                    'amount' => '49990',
                    'currency' => 'EUR',
                ],
                'quantity' => 1,
            ],
        ], $expectedBasketReadModel->getProducts());
        Assert::assertEquals('€499.90', $expectedBasketReadModel->getTotal());

        $this->scenario
            ->given([
                new BasketWasPickedUp(new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858')),
                new ProductWasAddedToBasket(
                    new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                ),
                new ProductWasAddedToBasket(
                    new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                    '2',
                    'LCD Monitor',
                    Money::EUR('49990'),
                    1
                ),
            ])
            ->when(
                new ProductWasRemovedFromBasket(
                    new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                    '1',
                    'Smartphone'
                )
            )
            ->then([$expectedBasketReadModel]);
    }

    /**
     * @test
     */
    public function it_updates_product_from_basket()
    {
        $expectedBasketReadModel = new BasketReadModel('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858');
        $expectedBasketReadModel->addProduct(
            new ProductWasAddedToBasket(
                new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                '1',
                'Smartphone',
                Money::EUR('9999'),
                1
            )
        );
        $expectedBasketReadModel->addProduct(
            new ProductWasAddedToBasket(
                new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                '2',
                'LCD Monitor',
                Money::EUR('49990'),
                1
            )
        );
        $expectedBasketReadModel->update(
            new ProductQuantityWasUpdated(
                new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                '1',
                Money::EUR('9999'),
                3
            )
        );
        Assert::assertFalse($expectedBasketReadModel->hasBeenCheckedOut());
        Assert::assertEquals([
            [
                'productId' => '1',
                'productName' => 'Smartphone',
                'productPrice' => [
                    'amount' => '9999',
                    'currency' => 'EUR',
                ],
                'quantity' => 3,
            ],
            [
                'productId' => '2',
                'productName' => 'LCD Monitor',
                'productPrice' => [
                    'amount' => '49990',
                    'currency' => 'EUR',
                ],
                'quantity' => 1,
            ],
        ], $expectedBasketReadModel->getProducts());
        Assert::assertEquals('€799.87', $expectedBasketReadModel->getTotal());

        $this->scenario
            ->given([
                new BasketWasPickedUp(new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858')),
                new ProductWasAddedToBasket(
                    new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                ),
                new ProductWasAddedToBasket(
                    new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                    '2',
                    'LCD Monitor',
                    Money::EUR('49990'),
                    1
                ),
            ])
            ->when(
                new ProductQuantityWasUpdated(
                    new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                    '1',
                    Money::EUR('9999'),
                    3
                )
            )
            ->then([$expectedBasketReadModel]);
    }

    /**
     * @test
     */
    public function it_checks_out_a_basket()
    {
        $expectedBasketReadModel = new BasketReadModel('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858');
        $expectedBasketReadModel->addProduct(
            new ProductWasAddedToBasket(
                new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                '1',
                'Smartphone',
                Money::EUR('9999'),
                1
            )
        );
        $expectedBasketReadModel->addProduct(
            new ProductWasAddedToBasket(
                new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                '2',
                'LCD Monitor',
                Money::EUR('49990'),
                1
            )
        );
        $expectedBasketReadModel->removeProduct(
            new ProductWasRemovedFromBasket(
                new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                '1',
                'Smartphone'
            )
        );
        $expectedBasketReadModel->setHasBeenCheckedOut();
        Assert::assertTrue($expectedBasketReadModel->hasBeenCheckedOut());
        Assert::assertEquals([
            [
                'productId' => '2',
                'productName' => 'LCD Monitor',
                'productPrice' => [
                    'amount' => '49990',
                    'currency' => 'EUR',
                ],
                'quantity' => 1,
            ],
        ], $expectedBasketReadModel->getProducts());
        Assert::assertEquals('€499.90', $expectedBasketReadModel->getTotal());

        $this->scenario
            ->given([
                new BasketWasPickedUp(new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858')),
                new ProductWasAddedToBasket(
                    new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                    '1',
                    'Smartphone',
                    Money::EUR('9999'),
                    1
                ),
                new ProductWasAddedToBasket(
                    new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                    '2',
                    'LCD Monitor',
                    Money::EUR('49990'),
                    1
                ),
                new ProductWasRemovedFromBasket(
                    new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858'),
                    '1',
                    'Smartphone'
                ),
            ])
            ->when(new BasketCheckedOut(new BasketId('480b8f7e-9dcc-4335-aecb-4bf4b4fb2858')))
            ->then([$expectedBasketReadModel]);
    }

    protected function createProjector(InMemoryRepository $repository): Projector
    {
        return new BasketProjector($repository);
    }
}
