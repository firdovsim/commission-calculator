<?php

function isEu($countryCode): string
{
    $euCountries = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES',
        'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU',
        'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK',
    ];

    return in_array($countryCode, $euCountries);
}

function readTransactions(string $filename): Generator
{
    $file = new SplFileObject($filename, 'r');
    while (!$file->eof()) {
        yield trim($file->fgets());
    }
}