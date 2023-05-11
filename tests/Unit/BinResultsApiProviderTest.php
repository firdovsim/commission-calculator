<?php

namespace Unit;

use Commission\Services\BinResultsApiProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class BinResultsApiProviderTest extends TestCase
{
    public function testGetBinResults()
    {
        $mockHandler = new MockHandler([
            new Response(200, [], '{"country": {"alpha2": "US"}}'),
            new Response(200, [], '{"country": {"alpha2": "FR"}}'),
        ]);

        $httpClient = new Client(['handler' => HandlerStack::create($mockHandler)]);
        $binResultsApiProvider = new BinResultsApiProvider($httpClient);

        $bin1 = '123456';
        $bin2 = '987654';
        $bin1Results = $binResultsApiProvider->getBinResults($bin1);
        $bin2Results = $binResultsApiProvider->getBinResults($bin2);

        $this->assertEquals(['country' => ['alpha2' => 'US']], $bin1Results);
        $this->assertEquals(['country' => ['alpha2' => 'FR']], $bin2Results);
    }

    public function testGetBinResultsWithHttpError()
    {
        $mockHandler = new MockHandler([
            new Response(404),
        ]);

        $httpClient = new Client(['handler' => HandlerStack::create($mockHandler)]);
        $binResultsApiProvider = new BinResultsApiProvider($httpClient);

        $this->expectException(\RuntimeException::class);
        $binResultsApiProvider->getBinResults('invalid-bin');
    }
}
