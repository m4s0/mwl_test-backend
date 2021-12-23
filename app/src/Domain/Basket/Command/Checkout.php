<?php

declare(strict_types=1);

namespace Domain\Basket\Command;

use Domain\Basket\ValueObject\BasketId;

class Checkout
{
    private BasketId $basketId;

    public function __construct(BasketId $basketId)
    {
        $this->basketId = $basketId;
    }

    public function getBasketId(): BasketId
    {
        return $this->basketId;
    }
}
