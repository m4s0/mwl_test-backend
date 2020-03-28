<?php

declare(strict_types=1);

namespace Basket\Event;

use Basket\ValueObject\BasketId;
use Broadway\Serializer\Serializable;
use Common\Service\MoneyParser;
use Money\Money;

class ProductQuantityWasUpdated implements Serializable
{
    private BasketId $basketId;
    private string   $productId;
    private Money    $productPrice;
    private int      $quantity;

    public function __construct(
        BasketId $basketId,
        string $productId,
        Money $productPrice,
        int $quantity
    ) {
        $this->basketId = $basketId;
        $this->productId = $productId;
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
            'productPrice' => $this->productPrice->jsonSerialize(),
            'quantity' => $this->quantity,
        ];
    }

    public static function deserialize(array $data)
    {
        return new self(
            new BasketId($data['basketId']),
            $data['productId'],
            MoneyParser::execute($data['productPrice']),
            $data['quantity']
        );
    }
}
