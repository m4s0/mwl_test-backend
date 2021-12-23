<?php

declare(strict_types=1);

namespace Domain\Basket\Command;

use Domain\Basket\ValueObject\BasketId;

class RemoveProductFromBasket
{
    private BasketId $basketId;
    private string   $productId;
    private string   $productName;

    public function __construct(
        BasketId $basketId,
        string $productId,
        string $productName
    ) {
        $this->basketId = $basketId;
        $this->productId = $productId;
        $this->productName = $productName;
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
}
