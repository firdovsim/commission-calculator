<?php

namespace Commission\Services;

use Commission\Contracts\BinResultsApiProviderInterface;
use Commission\Contracts\ExchangeRateApiClientInterface;

class TransactionFeeCalculator
{
    private const CURRENCY_EUR = 'EUR';
    private const ZERO_RATE = 0;
    private const COMMISSION_FOR_EUR = 0.01;
    private const COMMISSION_FOR_OTHERS = 0.02;

    private ExchangeRateApiClientInterface $exchangeRateApiClient;
    private BinResultsApiProviderInterface $binResultsApiProvider;

    public function __construct(
        ExchangeRateApiClientInterface $exchangeRateApiClient,
        BinResultsApiProviderInterface $binResultsApiProvider
    ) {
        $this->exchangeRateApiClient = $exchangeRateApiClient;
        $this->binResultsApiProvider = $binResultsApiProvider;
    }

    public function calculate(string $transactionDataJson): float
    {
        $transactionData = json_decode($transactionDataJson, true);
        $bin = $transactionData['bin'];
        $amount = $transactionData['amount'];
        $currency = $transactionData['currency'];

        $binData = $this->binResultsApiProvider->getBinResults($bin);
        if (!isset($binData['country']) || !isset($binData['country']['alpha2'])) {
            throw new \RuntimeException('Invalid bin data received');
        }
        $isEu = isEu($binData['country']['alpha2']);

        $fixedAmount = $this->calculateFixedAmount($currency, $amount);
        $feeAmount = $this->calculateFeeAmount($fixedAmount, $isEu);

        return $feeAmount;
    }

    private function calculateFixedAmount(string $currency, float $amount): float
    {
        $rate = $this->exchangeRateApiClient->getRateByCurrency($currency);

        if ($currency == self::CURRENCY_EUR || $rate == self::ZERO_RATE) {
            return $amount;
        }

        return $amount / $rate;
    }

    private function calculateFeeAmount(float $fixedAmount, bool $isEu): float
    {
        $fee = $isEu ? self::COMMISSION_FOR_EUR : self::COMMISSION_FOR_OTHERS;

        return $fixedAmount * $fee;
    }
}