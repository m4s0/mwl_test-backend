<?php

declare(strict_types=1);

namespace Domain\Product\Model;

use Money\Money;

class Product
{
    private string $productId;
    private string $productName;
    private Money  $productPrice;

    public function __construct(string $productId, string $productName, Money $productPrice)
    {
        $this->productId = $productId;
        $this->productName = $productName;
        $this->productPrice = $productPrice;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getProductPrice(): Money
    {
        return $this->productPrice;
    }
}
