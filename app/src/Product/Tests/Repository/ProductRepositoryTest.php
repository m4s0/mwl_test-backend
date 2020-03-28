<?php

declare(strict_types=1);

namespace Product\Tests\Repository;

use Money\Money;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Product\Model\Product;
use Product\Repository\FileSystem;
use Product\Repository\ProductRepository;

class ProductRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function find_product_by_id()
    {
        $filesystem = $this->prophesize(FileSystem::class);
        $filesystem->getFileContent('filename.json')
            ->willReturn(
                '
                {
                  "items": [
                    {
                      "id": 1,
                      "name": "Product 1",
                      "price": 1000
                    },
                    {
                      "id": 2,
                      "name": "Product 2",
                      "price": 9.90
                    }
                  ]
                }
                '
            );
        $productRepository = new ProductRepository('filename.json', $filesystem->reveal());

        Assert::assertEquals(
            new Product('1', 'Product 1', Money::EUR(100000)),
            $productRepository->find('1')
        );

        Assert::assertEquals(
            new Product('2', 'Product 2', Money::EUR('990')),
            $productRepository->find('2')
        );
    }

    /**
     * @test
     */
    public function find_all_products()
    {
        $filesystem = $this->prophesize(FileSystem::class);
        $filesystem->getFileContent('filename.json')
            ->willReturn(
                '
                {
                  "items": [
                    {
                      "id": 1,
                      "name": "Product 1",
                      "price": 1000
                    },
                    {
                      "id": 2,
                      "name": "Product 2",
                      "price": 9.90
                    }
                  ]
                }
                '
            );
        $productRepository = new ProductRepository('filename.json', $filesystem->reveal());

        Assert::assertEquals([
            new Product('1', 'Product 1', Money::EUR(100000)),
            new Product('2', 'Product 2', Money::EUR('990')),
        ], $productRepository->findAll());
    }
}
