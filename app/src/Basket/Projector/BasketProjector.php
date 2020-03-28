<?php

declare(strict_types=1);

namespace Basket\Projector;

use Basket\Event\BasketCheckedOut;
use Basket\Event\BasketWasPickedUp;
use Basket\Event\ProductQuantityWasUpdated;
use Basket\Event\ProductWasAddedToBasket;
use Basket\Event\ProductWasRemovedFromBasket;
use Basket\ReadModel\BasketReadModel;
use Broadway\ReadModel\Projector;
use Broadway\ReadModel\Repository;

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
