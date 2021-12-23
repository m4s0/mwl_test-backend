<?php

declare(strict_types=1);

namespace Domain\Basket\Event;

use Broadway\Serializer\Serializable;
use Domain\Basket\ValueObject\BasketId;

class BasketWasPickedUp implements Serializable
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

    public function serialize(): array
    {
        return ['basketId' => (string) $this->basketId];
    }

    public static function deserialize(array $data): BasketWasPickedUp
    {
        return new self(new BasketId($data['basketId']));
    }
}
