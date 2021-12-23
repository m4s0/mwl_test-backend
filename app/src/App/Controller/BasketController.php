<?php

declare(strict_types=1);

namespace App\Controller;

use Broadway\CommandHandling\CommandBus;
use Broadway\ReadModel\Repository;
use Broadway\Repository\AggregateNotFoundException;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use Domain\Basket\Command\AddProductToBasket;
use Domain\Basket\Command\Checkout;
use Domain\Basket\Command\PickUpBasket;
use Domain\Basket\Command\RemoveProductFromBasket;
use Domain\Basket\Command\UpdateProductQuantity;
use Domain\Basket\Exception\BasketException;
use Domain\Basket\Exception\ProductNotInBasketException;
use Domain\Basket\ValueObject\BasketId;
use Domain\Product\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BasketController
{
    private CommandBus $commandBus;
    private UuidGeneratorInterface $uuidGenerator;
    private Repository $repository;
    private ProductRepository $productRepository;

    public function __construct(
        CommandBus $commandBus,
        UuidGeneratorInterface $uuidGenerator,
        Repository $repository,
        ProductRepository $productRepository
    ) {
        $this->commandBus = $commandBus;
        $this->uuidGenerator = $uuidGenerator;
        $this->repository = $repository;
        $this->productRepository = $productRepository;
    }

    public function pickUpBasket(): JsonResponse
    {
        $basketId = new BasketId($this->uuidGenerator->generate());
        $command = new PickUpBasket($basketId);

        $this->commandBus->dispatch($command);

        return new JsonResponse(['id' => (string) $basketId]);
    }

    public function addProductToBasket(Request $request, string $basketId): JsonResponse
    {
        $product = $this->productRepository->find((string) $request->request->get('productId'));
        if (null === $product) {
            return new JsonResponse(['Product not found'], Response::HTTP_NOT_FOUND);
        }

        $command = new AddProductToBasket(
            new BasketId($basketId),
            $product->getProductId(),
            $product->getProductName(),
            $product->getProductPrice(),
            (int) $request->request->get('quantity', 1)
        );

        try {
            $this->commandBus->dispatch($command);
        } catch (AggregateNotFoundException $e) {
            return new JsonResponse(['Basket not found'], Response::HTTP_NOT_FOUND);
        } catch (BasketException $e) {
            return new JsonResponse(['errorMessage' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse();
    }

    public function updateProductBasket(Request $request, string $basketId): JsonResponse
    {
        $productId = (string) $request->request->get('productId');
        $quantity = (int) $request->request->get('quantity');

        if (!$productId || !$quantity) {
            throw new BadRequestHttpException();
        }

        $product = $this->productRepository->find($productId);
        if (null === $product) {
            return new JsonResponse(['Product not found'], Response::HTTP_NOT_FOUND);
        }

        $command = new UpdateProductQuantity(
            new BasketId($basketId),
            $product->getProductId(),
            $product->getProductPrice(),
            $quantity
        );

        try {
            $this->commandBus->dispatch($command);
        } catch (BasketException $e) {
            return new JsonResponse(['errorMessage' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse();
    }

    public function removeProductFromBasket(string $basketId, string $productId): JsonResponse
    {
        $product = $this->productRepository->find($productId);
        if (null === $product) {
            return new JsonResponse(['Product not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->commandBus->dispatch(
                new RemoveProductFromBasket(
                    new BasketId($basketId),
                    $productId,
                    $product->getProductName()
                )
            );
        } catch (ProductNotInBasketException $e) {
            return new JsonResponse(['errorMessage' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (BasketException $e) {
            return new JsonResponse(['errorMessage' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([], Response::HTTP_ACCEPTED);
    }

    public function checkout(string $basketId): JsonResponse
    {
        try {
            $this->commandBus->dispatch(new Checkout(new BasketId($basketId)));
        } catch (BasketException $e) {
            return new JsonResponse(['errorMessage' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse();
    }

    public function getBasket(string $basketId): JsonResponse
    {
        $readModel = $this->repository->find(new BasketId($basketId));
        if (!$readModel) {
            return new JsonResponse(['error' => 'Basket not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($readModel->serialize());
    }

    public function getBaskets(): JsonResponse
    {
        $readModels = $this->repository->findAll();

        $models = [];
        foreach ($readModels as $readModel) {
            $models[] = $readModel->serialize();
        }

        return new JsonResponse($models);
    }
}
