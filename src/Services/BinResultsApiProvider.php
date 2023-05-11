<?php

namespace Commission\Services;

use Commission\Contracts\BinResultsApiProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class BinResultsApiProvider implements BinResultsApiProviderInterface
{
    private string $baseUrl = 'https://lookup.binlist.net';
    private Client $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getBinResults(string $bin): array
    {
        $url = $this->baseUrl . '/' . $bin;

        try {
            $response = $this->httpClient->get($url);
        } catch (RequestException $e) {
            throw new \RuntimeException('Error fetching BIN results');
        }

        $results = $response->getBody()->getContents();

        return @json_decode($results, true);
    }
}