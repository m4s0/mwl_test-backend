<?php

declare(strict_types=1);

namespace Product\Tests\Model;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Product\Model\Product;

class ProductTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_creates_a_valid_product()
    {
        $moneyParser = new DecimalMoneyParser(new ISOCurrencies());
        $productPrice = $moneyParser->parse('79.90', new Currency('EUR'));

        $product = new Product('1', 'Foo Product', $productPrice);

        Assert::assertEquals('1', $product->getProductId());
        Assert::assertEquals('Foo Product', $product->getProductName());
        Assert::assertEquals(Money::EUR('7990'), $product->getProductPrice());
    }
}
