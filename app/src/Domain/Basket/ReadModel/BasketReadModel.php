<?php

declare(strict_types=1);

namespace Domain\Basket\ReadModel;

use Broadway\ReadModel\SerializableReadModel;
use Domain\Basket\Event\ProductQuantityWasUpdated;
use Domain\Basket\Event\ProductWasAddedToBasket;
use Domain\Basket\Event\ProductWasRemovedFromBasket;
use Domain\Common\Service\MoneyFormatter;
use Domain\Common\Service\MoneyParser;
use Money\Money;

class BasketReadModel implements SerializableReadModel
{
    protected string $basketId;
    protected array  $products;
    protected array  $removedProducts;
    protected bool   $hasBeenCheckedOut;
    protected Money  $total;

    public function __construct(string $basketId)
    {
        $this->basketId = $basketId;
        $this->products = [];
        $this->removedProducts = [];
        $this->hasBeenCheckedOut = false;
        $this->total = Money::EUR(0);
    }

    public function getId(): string
    {
        return $this->basketId;
    }

    public function setHasBeenCheckedOut(): void
    {
        $this->hasBeenCheckedOut = true;
    }

    public function hasBeenCheckedOut(): bool
    {
        return $this->hasBeenCheckedOut;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getRemovedProducts(): array
    {
        return $this->removedProducts;
    }

    public function getTotal(): string
    {
        return MoneyFormatter::execute($this->total);
    }

    public function serialize(): array
    {
        return [
            'basketId' => $this->basketId,
            'products' => $this->products,
            'removedProducts' => $this->removedProducts,
            'hasBeenCheckedOut' => $this->hasBeenCheckedOut,
            'total' => $this->total->jsonSerialize(),
        ];
    }

    public static function deserialize(array $data): BasketReadModel
    {
        $readModel = new self($data['basketId']);

        $readModel->products = self::deserializeProducts($data['products']);
        $readModel->removedProducts = self::deserializeProducts($data['removedProducts']);
        $readModel->hasBeenCheckedOut = $data['hasBeenCheckedOut'];
        $readModel->total = MoneyParser::execute($data['total']);

        return $readModel;
    }

    public function addProduct(ProductWasAddedToBasket $event): void
    {
        foreach ($this->products as $index => $product) {
            if ($product['productId'] === $event->getProductId()) {
                $this->products[$index]['quantity'] += $event->getQuantity();
                $this->total = $this->total->add(
                    $event->getProductPrice()->multiply($event->getQuantity())
                );

                return;
            }
        }

        $this->products[] = [
            'productId' => $event->getProductId(),
            'productName' => $event->getProductName(),
            'productPrice' => $event->getProductPrice()->jsonSerialize(),
            'quantity' => $event->getQuantity(),
        ];

        $this->total = $this->total->add(
            $event->getProductPrice()->multiply($event->getQuantity())
        );
    }

    public function removeProduct(ProductWasRemovedFromBasket $event): void
    {
        $result = $this->findProduct($this->products, $event->getProductId());
        $index = $result['index'];
        $product = $result['product'];

        if (!$product) {
            return;
        }

        $productPrice = MoneyParser::execute($product['productPrice']);
        $subtrahends = $productPrice->multiply($this->products[$index]['quantity']);
        $this->total = $this->total->subtract($subtrahends);
        unset($this->products[$index]);
        $this->products = array_values($this->products);

        $this->removedProducts[] = $product;
    }

    public function update(ProductQuantityWasUpdated $event): void
    {
        foreach ($this->products as $index => $product) {
            if ($product['productId'] === $event->getProductId()) {
                $this->total = $this->total->subtract(
                    $event->getProductPrice()->multiply($this->products[$index]['quantity'])
                );

                $this->products[$index]['quantity'] = $event->getQuantity();
                $this->total = $this->total->add(
                    $event->getProductPrice()->multiply($event->getQuantity())
                );

                return;
            }
        }
    }

    protected static function deserializeProducts(array $products): array
    {
        $result = [];
        foreach ($products as $product) {
            $products['productPrice'] = MoneyFormatter::execute(MoneyParser::execute($product['productPrice']));
            $result[] = $product;
        }

        return $result;
    }

    protected function findProduct(array $products, string $productId): ?array
    {
        foreach ($products as $index => $product) {
            if ($product['productId'] === $productId) {
                return [
                    'index' => $index,
                    'product' => $product,
                ];
            }
        }

        return null;
//        foreach ($this->products as $index => $product) {
//            if ($product['productId'] === $event->getProductId()) {
//                $productPrice = MoneyParser::execute($product['productPrice']);
//                $subtrahends  = $productPrice->multiply($this->products[$index]['quantity']);
//                $this->total  = $this->total->subtract($subtrahends);
//                unset($this->products[$index]);
//                $this->products = array_values($this->products);
//            }
//        }
    }
}
