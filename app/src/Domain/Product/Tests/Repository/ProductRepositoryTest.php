<?php

declare(strict_types=1);

namespace Domain\Product\Tests\Repository;

use Domain\Product\Model\Product;
use Domain\Product\Repository\FileSystem;
use Domain\Product\Repository\ProductRepository;
use Money\Money;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ProductRepositoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function find_product_by_id(): void
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
    public function find_all_products(): void
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
