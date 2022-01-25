<?php

declare(strict_types=1);

namespace Domain\Product\Tests\Model;

use Domain\Product\Model\Product;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_creates_a_valid_product(): void
    {
        $moneyParser = new DecimalMoneyParser(new ISOCurrencies());
        $productPrice = $moneyParser->parse('79.90', new Currency('EUR'));

        $product = new Product('1', 'Foo Product', $productPrice);

        Assert::assertEquals('1', $product->getProductId());
        Assert::assertEquals('Foo Product', $product->getProductName());
        Assert::assertEquals(Money::EUR('7990'), $product->getProductPrice());
    }
}
