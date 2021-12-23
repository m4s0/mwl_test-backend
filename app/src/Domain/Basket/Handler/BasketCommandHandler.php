<?php

declare(strict_types=1);

namespace Domain\Basket\Handler;

use Broadway\CommandHandling\SimpleCommandHandler;
use Domain\Basket\Aggregate\Basket;
use Domain\Basket\Command\AddProductToBasket;
use Domain\Basket\Command\Checkout;
use Domain\Basket\Command\PickUpBasket;
use Domain\Basket\Command\RemoveProductFromBasket;
use Domain\Basket\Command\UpdateProductQuantity;
use Domain\Basket\Repository\BasketRepository;

class BasketCommandHandler extends SimpleCommandHandler
{
    private BasketRepository $repository;

    public function __construct(BasketRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handlePickUpBasket(PickUpBasket $command): void
    {
        $basket = Basket::pickUpBasket($command->getBasketId());

        $this->repository->save($basket);
    }

    public function handleAddProductToBasket(AddProductToBasket $command): void
    {
        $basket = $this->repository->load($command->getBasketId());

        $basket->addProduct($command);

        $this->repository->save($basket);
    }

    public function handleRemoveProductFromBasket(RemoveProductFromBasket $command): void
    {
        $basket = $this->repository->load($command->getBasketId());
        $basket->removeProduct($command);

        $this->repository->save($basket);
    }

    public function handleUpdateProductQuantity(UpdateProductQuantity $command): void
    {
        $basket = $this->repository->load($command->getBasketId());
        $basket->updateProduct($command);

        $this->repository->save($basket);
    }

    public function handleCheckout(Checkout $command): void
    {
        $basket = $this->repository->load($command->getBasketId());
        $basket->checkout();

        $this->repository->save($basket);
    }
}
