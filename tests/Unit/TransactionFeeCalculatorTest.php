<?php

namespace Unit;

use Commission\Contracts\BinResultsApiProviderInterface;
use Commission\Contracts\ExchangeRateApiClientInterface;
use Commission\Services\TransactionFeeCalculator;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class TransactionFeeCalculatorTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCalculate()
    {
        $exchangeRateApiClient = $this->createMock(ExchangeRateApiClientInterface::class);
        $binResultsApiProvider = $this->createMock(BinResultsApiProviderInterface::class);

        $exchangeRateApiClient->method('getRateByCurrency')->willReturn(1);
        $binResultsApiProvider->method('getBinResults')->willReturn([
            'country' => [
                'alpha2' => 'GB'
            ]
        ]);

        $calculator = new TransactionFeeCalculator($exchangeRateApiClient, $binResultsApiProvider);
        $result = $calculator->calculate('{"bin": "123456", "amount": 100, "currency": "GBP"}');

        $this->assertEquals(2, $result);
    }
}