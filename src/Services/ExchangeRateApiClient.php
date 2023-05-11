<?php

namespace Commission\Services;

use Commission\Contracts\ExchangeRateApiClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ExchangeRateApiClient implements ExchangeRateApiClientInterface
{
    private string $baseUrl = 'https://api.exchangeratesapi.io/latest';
    private Client $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getRateByCurrency($currency): float
    {
        $url = $this->baseUrl;

        try {
            $response = $this->httpClient->get($url);
        } catch (RequestException $e) {
            throw new \RuntimeException('Error fetching exchange rates');
        }

        $results = $response->getBody()->getContents();
        $rates = @json_decode($results, true)['rates'][$currency];

        return (float) $rates;
    }
}