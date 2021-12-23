<?php

declare(strict_types=1);

namespace Domain\Basket\Command;

use Domain\Basket\ValueObject\BasketId;
use Money\Money;

class AddProductToBasket
{
    private BasketId $basketId;
    private string   $productId;
    private string   $productName;
    private Money    $productPrice;
    private int      $quantity;

    public function __construct(
        BasketId $basketId,
        string $productId,
        string $productName,
        Money $productPrice,
        int $quantity
    ) {
        $this->basketId = $basketId;
        $this->productId = $productId;
        $this->productName = $productName;
        $this->productPrice = $productPrice;
        $this->quantity = $quantity;
    }

    public function getBasketId(): BasketId
    {
        return $this->basketId;
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

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
