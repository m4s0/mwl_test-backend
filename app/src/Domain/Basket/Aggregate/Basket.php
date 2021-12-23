<?php

declare(strict_types=1);

namespace Domain\Basket\Aggregate;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Domain\Basket\Command\AddProductToBasket;
use Domain\Basket\Command\RemoveProductFromBasket;
use Domain\Basket\Command\UpdateProductQuantity;
use Domain\Basket\Event\BasketCheckedOut;
use Domain\Basket\Event\BasketWasPickedUp;
use Domain\Basket\Event\ProductQuantityWasUpdated;
use Domain\Basket\Event\ProductWasAddedToBasket;
use Domain\Basket\Event\ProductWasRemovedFromBasket;
use Domain\Basket\Exception\BasketAlreadyCheckedOutException;
use Domain\Basket\Exception\EmptyBasketException;
use Domain\Basket\Exception\ProductNotInBasketException;
use Domain\Basket\Exception\ProductQuantityException;
use Domain\Basket\ValueObject\BasketId;

class Basket extends EventSourcedAggregateRoot
{
    private BasketId $basketId;
    private array    $products = [];
    private bool     $hasBeenCheckedOut = false;

    public function getAggregateRootId(): string
    {
        return (string) $this->basketId;
    }

    public static function pickUpBasket(BasketId $basketId): Basket
    {
        $basket = new self();
        $basket->pickUp($basketId);

        return $basket;
    }

    public function addProduct(AddProductToBasket $command): void
    {
        if ($this->hasBeenCheckedOut) {
            throw new BasketAlreadyCheckedOutException('Basket already checked out');
        }

        if ($command->getQuantity() < 1) {
            throw new ProductQuantityException('Product quantity not valid.');
        }

        $this->apply(
            new ProductWasAddedToBasket(
                $this->basketId,
                $command->getProductId(),
                $command->getProductName(),
                $command->getProductPrice(),
                $command->getQuantity()
            )
        );
    }

    public function updateProduct(UpdateProductQuantity $command): void
    {
        if ($this->hasBeenCheckedOut) {
            throw new BasketAlreadyCheckedOutException('Basket already checked out');
        }

        if (!$this->productIsInBasket($command->getProductId())) {
            throw new ProductNotInBasketException('Product not in Basket.');
        }

        if ($command->getQuantity() < 1) {
            throw new ProductQuantityException('Product quantity not valid.');
        }

        $this->apply(
            new ProductQuantityWasUpdated(
                $this->basketId,
                $command->getProductId(),
                $command->getProductPrice(),
                $command->getQuantity(),
            )
        );
    }

    public function removeProduct(RemoveProductFromBasket $command): void
    {
        if ($this->hasBeenCheckedOut) {
            throw new BasketAlreadyCheckedOutException('Basket already checked out');
        }

        if (!$this->productIsInBasket($command->getProductId())) {
            throw new ProductNotInBasketException('Product not in Basket.');
        }

        $this->apply(
            new ProductWasRemovedFromBasket(
                $this->basketId,
                $command->getProductId(),
                $command->getProductName()
            )
        );
    }

    public function checkout(): void
    {
        if ($this->hasBeenCheckedOut) {
            throw new BasketAlreadyCheckedOutException('Basket already checked out');
        }

        if (0 === count($this->products) || array_sum($this->products) < 1) {
            throw new EmptyBasketException('Cannot checkout an empty basket');
        }

        $this->apply(
            new BasketCheckedOut($this->basketId)
        );
    }

    protected function applyBasketWasPickedUp(BasketWasPickedUp $event): void
    {
        $this->basketId = $event->getBasketId();
    }

    protected function applyProductWasAddedToBasket(ProductWasAddedToBasket $event): void
    {
        $productId = $event->getProductId();

        if (!$this->productIsInBasket($productId)) {
            $this->products[$productId] = 0;
        }

        ++$this->products[$productId];
    }

    protected function applyProductWasRemovedFromBasket(ProductWasRemovedFromBasket $event): void
    {
        $this->products[$event->getProductId()] = 0;
    }

    protected function applyProductQuantityWasUpdated(ProductQuantityWasUpdated $event): void
    {
        $productId = $event->getProductId();
        $quantity = $event->getQuantity();

        $this->products[$productId] = $quantity;
    }

    protected function applyBasketCheckedOut(BasketCheckedOut $event): void
    {
        $this->hasBeenCheckedOut = true;
    }

    private function pickUp(BasketId $basketId): void
    {
        $this->apply(
            new BasketWasPickedUp($basketId)
        );
    }

    private function productIsInBasket($productId): bool
    {
        return count($this->products) > 0 && isset($this->products[$productId]) && $this->products[$productId] > 0;
    }
}