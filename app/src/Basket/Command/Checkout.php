<?php

declare(strict_types=1);

namespace Basket\Command;

use Basket\ValueObject\BasketId;

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
