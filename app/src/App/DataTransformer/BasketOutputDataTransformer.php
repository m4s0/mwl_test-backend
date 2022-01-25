<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\BasketOutput;
use Domain\Basket\ReadModel\BasketReadModel;

class BasketOutputDataTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($data, string $to, array $context = []): BasketOutput
    {
        assert($data instanceof BasketReadModel);

        $output = new BasketOutput();
        $output->basketId = $data->getId();
        $output->products = $data->getProducts();
        $output->removedProducts = $data->getRemovedProducts();
        $output->total = $data->getTotal();
        $output->hasBeenCheckedOut = $data->hasBeenCheckedOut();

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return true;

        return BasketOutput::class === $to && $data instanceof BasketReadModel;
    }
}