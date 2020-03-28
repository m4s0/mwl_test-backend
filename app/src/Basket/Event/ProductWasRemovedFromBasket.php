<?php

declare(strict_types=1);

namespace Basket\Event;

use Basket\ValueObject\BasketId;
use Broadway\Serializer\Serializable;

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

    public static function deserialize(array $data)
    {
        return new self(
            new BasketId($data['basketId']),
            $data['productId'],
            $data['productName']
        );
    }
}
