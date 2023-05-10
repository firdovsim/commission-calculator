<?php

define('BASE_DIR', dirname(__DIR__));

require BASE_DIR . '/vendor/autoload.php';

const CURRENCY_EUR = 'EUR';
const ZERO_RATE = 0;
const FEE_FOR_EUR = 0.01;
const FEE_FOR_OTHERS = 0.02;

function getRateByCurrency($currency)
{
    $results = file_get_contents('https://api.exchangeratesapi.io/latest');

    return @json_decode($results, true)['rates'][$currency];
}

if (! isset($argv[1])) {
    throw new Exception('Argument not provided');
}

$filename = $argv[1];
$input = file_get_contents($filename);
$transactions = explode("\n", $input);

foreach ($transactions as $transaction) {
    if (empty($transaction)) {
        break;
    }

    $transactionData = json_decode($transaction, true);

    $bin = $transactionData['bin'];
    $amount = $transactionData['amount'];
    $currency = $transactionData['currency'];

    $binData = getBinResults($bin);

    $isEu = isEu($binData['country']['alpha2']);

    $rate = getRateByCurrency($currency);
    if ($currency == CURRENCY_EUR || $rate == ZERO_RATE) {
        $fixedAmount = $amount;
    } else {
        $fixedAmount = $amount / $rate;
    }

    $fee = $isEu ? FEE_FOR_EUR : FEE_FOR_OTHERS;
    $feeAmount = $fixedAmount * $fee;

    echo $feeAmount . PHP_EOL;
}

