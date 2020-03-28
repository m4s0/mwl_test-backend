<?php

declare(strict_types=1);

namespace Basket\Tests\ReadModel;

use Basket\Event\ProductQuantityWasUpdated;
use Basket\Event\ProductWasAddedToBasket;
use Basket\Event\ProductWasRemovedFromBasket;
use Basket\ReadModel\BasketReadModel;
use Basket\ValueObject\BasketId;
use Money\Money;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class BasketReadModelTest extends TestCase
{
    /**
     * @test
     */
    public function serialize()
    {
        $basketReadModel = new BasketReadModel('dce0d45d-da31-4c30-b5c9-d210dc702f7d');
        $basketReadModel->addProduct(
            new ProductWasAddedToBasket(
                new BasketId('dce0d45d-da31-4c30-b5c9-d210dc702f7d'),
                '1',
                'Smartphone',
                Money::EUR(2999000),
                1
            )
        );
        $basketReadModel->addProduct(
            new ProductWasAddedToBasket(
                new BasketId('dce0d45d-da31-4c30-b5c9-d210dc702f7d'),
                '1',
                'Smartphone',
                Money::EUR(2999000),
                1
            )
        );
        $basketReadModel->addProduct(
            new ProductWasAddedToBasket(
                new BasketId('dce0d45d-da31-4c30-b5c9-d210dc702f7d'),
                '2',
                'LCD TV',
                Money::EUR(5999000),
                1
            )
        );
        $basketReadModel->removeProduct(
            new ProductWasRemovedFromBasket(
                new BasketId('dce0d45d-da31-4c30-b5c9-d210dc702f7d'),
                '1',
                'Smartphone'
            )
        );
        $basketReadModel->update(
            new ProductQuantityWasUpdated(
                new BasketId('dce0d45d-da31-4c30-b5c9-d210dc702f7d'),
                '2',
                Money::EUR(5999000),
                3
            )
        );

        Assert::assertEquals([
            'basketId' => 'dce0d45d-da31-4c30-b5c9-d210dc702f7d',
            'products' => [
                [
                    'productId' => '2',
                    'productName' => 'LCD TV',
                    'productPrice' => [
                        'amount' => '5999000',
                        'currency' => 'EUR',
                    ],
                    'quantity' => 3,
                ],
            ],
            'removedProducts' => [
                [
                    'productId' => '1',
                    'productName' => 'Smartphone',
                    'productPrice' => [
                        'amount' => '2999000',
                        'currency' => 'EUR',
                    ],
                    'quantity' => 2,
                ],
            ],
            'hasBeenCheckedOut' => false,
            'total' => [
                'amount' => '17997000',
                'currency' => 'EUR',
            ],
        ], $basketReadModel->serialize());
    }

    /**
     * @test
     */
    public function deserialize()
    {
        $data = [
            'basketId' => 'dce0d45d-da31-4c30-b5c9-d210dc702f7d',
            'products' => [],
            'removedProducts' => [],
            'hasBeenCheckedOut' => false,
            'total' => [
                'amount' => '1000',
                'currency' => 'EUR',
            ],
        ];

        $basketReadModel = BasketReadModel::deserialize($data);
        Assert::assertEquals('dce0d45d-da31-4c30-b5c9-d210dc702f7d', $basketReadModel->getId());
        Assert::assertEquals([], $basketReadModel->getProducts());
        Assert::assertEquals([], $basketReadModel->getRemovedProducts());
        Assert::assertEquals(false, $basketReadModel->hasBeenCheckedOut());
        Assert::assertEquals('€10.00', $basketReadModel->getTotal());
    }
}
