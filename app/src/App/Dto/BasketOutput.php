<?php

declare(strict_types=1);

namespace App\Dto;

use Money\Money;

class BasketOutput
{
    public string $basketId;
    public array  $products;
    public array  $removedProducts;
    public bool   $hasBeenCheckedOut;
    public Money  $total;
}