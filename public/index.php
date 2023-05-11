<?php

use Commission\Services\BinResultsApiProvider;
use Commission\Services\ExchangeRateApiClient;
use Commission\Services\TransactionFeeCalculator;
use GuzzleHttp\Client;

define('BASE_DIR', dirname(__DIR__));

require BASE_DIR . '/vendor/autoload.php';

if (empty($argv[1])) {
    throw new InvalidArgumentException('Filename argument is required');
}

$filename = $argv[1];

$client = new Client();

$exchangeRateApiClient = new ExchangeRateApiClient($client);
$binResultsApiProvider = new BinResultsApiProvider($client);

$feeCalculator = new TransactionFeeCalculator($exchangeRateApiClient, $binResultsApiProvider);

$transactions = readTransactions($filename);
foreach ($transactions as $transaction) {
    if (empty($transaction)) {
        break;
    }

    $feeAmount = $feeCalculator->calculate($transaction);
    echo $feeAmount . PHP_EOL;
}