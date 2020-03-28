<?php

declare(strict_types=1);

namespace App\Controller;

use Common\Service\MoneyFormatter;
use Product\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProducts(Request $request): Response
    {
        $data = [];
        foreach ($this->productRepository->findAll() as $product) {
            $data[] = [
                'id' => $product->getProductId(),
                'name' => $product->getProductName(),
                'price' => MoneyFormatter::execute($product->getProductPrice()),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
}
