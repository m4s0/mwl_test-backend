<?php

declare(strict_types=1);

namespace Domain\Basket\Projector;

use Broadway\ReadModel\Projector;
use Broadway\ReadModel\Repository;
use Domain\Basket\Event\BasketCheckedOut;
use Domain\Basket\Event\BasketWasPickedUp;
use Domain\Basket\Event\ProductQuantityWasUpdated;
use Domain\Basket\Event\ProductWasAddedToBasket;
use Domain\Basket\Event\ProductWasRemovedFromBasket;
use Domain\Basket\ReadModel\BasketReadModel;

class BasketProjector extends Projector
{
    protected Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    protected function applyBasketWasPickedUp(BasketWasPickedUp $event): void
    {
        $readModel = new BasketReadModel((string) $event->getBasketId());

        $this->repository->save($readModel);
    }

    protected function applyProductWasAddedToBasket(ProductWasAddedToBasket $event): void
    {
        $readModel = $this->repository->find($event->getBasketId());

        $readModel->addProduct($event);
        $this->repository->save($readModel);
    }

    protected function applyProductWasRemovedFromBasket(ProductWasRemovedFromBasket $event): void
    {
        $readModel = $this->repository->find($event->getBasketId());

        $readModel->removeProduct($event);
        $this->repository->save($readModel);
    }

    protected function applyProductQuantityWasUpdated(ProductQuantityWasUpdated $event): void
    {
        $readModel = $this->repository->find($event->getBasketId());

        $readModel->update($event);
        $this->repository->save($readModel);
    }

    protected function applyBasketCheckedOut(BasketCheckedOut $event): void
    {
        $readModel = $this->repository->find($event->getBasketId());

        $readModel->setHasBeenCheckedOut();
        $this->repository->save($readModel);
    }
}
