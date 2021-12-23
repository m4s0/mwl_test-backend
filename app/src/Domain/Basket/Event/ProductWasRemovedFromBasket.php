<?php

declare(strict_types=1);

namespace Domain\Basket\Event;

use Broadway\Serializer\Serializable;
use Domain\Basket\ValueObject\BasketId;

class ProductWasRemovedFromBasket implements Serializable
{
    private BasketId $basketId;
    private string $productId;
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

    public function serialize(): array
    {
        return [
            'basketId' => (string) $this->basketId,
            'productId' => $this->productId,
            'productName' => $this->productName,
        ];
    }

    public static function deserialize(array $data): ProductWasRemovedFromBasket
    {
        return new self(
            new BasketId($data['basketId']),
            $data['productId'],
            $data['productName']
        );
    }
}
