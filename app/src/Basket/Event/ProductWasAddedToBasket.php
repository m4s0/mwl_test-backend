<?php

declare(strict_types=1);

namespace Basket\Event;

use Basket\ValueObject\BasketId;
use Broadway\Serializer\Serializable;
use Common\Service\MoneyParser;
use Money\Money;

class ProductWasAddedToBasket implements Serializable
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

    public function serialize(): array
    {
        return [
            'basketId' => (string) $this->basketId,
            'productId' => $this->productId,
            'productName' => $this->productName,
            'productPrice' => $this->productPrice->jsonSerialize(),
            'quantity' => $this->quantity,
        ];
    }

    public static function deserialize(array $data): ProductWasAddedToBasket
    {
        return new self(
            new BasketId($data['basketId']),
            $data['productId'],
            $data['productName'],
            MoneyParser::execute($data['productPrice']),
            $data['quantity']
        );
    }
}
