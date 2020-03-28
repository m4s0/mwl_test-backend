<?php

declare(strict_types=1);

namespace Common\Service;

use Money\Currency;
use Money\Money;

class MoneyParser
{
    public static function execute(array $data): Money
    {
        return new Money($data['amount'], new Currency($data['currency']));
    }
}
