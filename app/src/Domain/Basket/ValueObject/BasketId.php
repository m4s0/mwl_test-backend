<?php

declare(strict_types=1);

namespace Domain\Basket\ValueObject;

use Assert\Assertion as Assert;

final class BasketId
{
    private string $basketId;

    public function __construct(string $basketId)
    {
        Assert::uuid($basketId);

        $this->basketId = $basketId;
    }

    public function __toString(): string
    {
        return $this->basketId;
    }
}
