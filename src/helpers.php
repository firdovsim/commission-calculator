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

function getBinResults($bin)
{
    $url = 'https://lookup.binlist.net/' . $bin;
    $results = file_get_contents($url);
    if (! $results) {
        throw new Exception('Failed to fetch data from ' . $url);
    }
    return json_decode($results, true);
}