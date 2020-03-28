<?php

declare(strict_types=1);

namespace Basket\Handler;

use Basket\Aggregate\Basket;
use Basket\Command\AddProductToBasket;
use Basket\Command\Checkout;
use Basket\Command\PickUpBasket;
use Basket\Command\RemoveProductFromBasket;
use Basket\Command\UpdateProductQuantity;
use Basket\Repository\BasketRepository;
use Broadway\CommandHandling\SimpleCommandHandler;

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
