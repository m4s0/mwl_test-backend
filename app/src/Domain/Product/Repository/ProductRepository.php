<?php

declare(strict_types=1);

namespace Domain\Product\Repository;

use Domain\Product\Model\Product;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;

class ProductRepository
{
    private array $products = [];

    public function __construct(string $filename, FileSystemInterface $filesystem)
    {
        $content = $filesystem->getFileContent($filename);
        $moneyParser = new DecimalMoneyParser(new ISOCurrencies());

        foreach (json_decode($content, true)['items'] as $item) {
            $productPrice = $moneyParser->parse((string) $item['price'], new Currency('EUR'));
            $this->products[] = new Product((string) $item['id'], $item['name'], $productPrice);
        }
    }

    public function find(string $productId): ?Product
    {
        foreach ($this->products as $index => $product) {
            if ($productId === $product->getProductId()) {
                return $product;
            }
        }
    }

    public function findAll(): array
    {
        return $this->products;
    }
}
