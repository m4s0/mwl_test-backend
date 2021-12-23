<?php

declare(strict_types=1);

namespace Domain\Common\Service;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;

class MoneyFormatter
{
    public static function execute(Money $money): string
    {
        $numberFormatter = new NumberFormatter('en', NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

        return $moneyFormatter->format($money);
    }
}
