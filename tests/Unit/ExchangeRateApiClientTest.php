<?php

namespace Unit;

use Commission\Services\ExchangeRateApiClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class ExchangeRateApiClientTest extends TestCase
{
    private ExchangeRateApiClient $client;
    private Client|MockObject $httpClient;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(Client::class);
        $this->client = new ExchangeRateApiClient($this->httpClient);
    }

    /**
     * @throws Exception
     */
    public function testGetRateByCurrency()
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $streamMock = $this->createMock(StreamInterface::class);

        $httpClientMock = $this->createMock(Client::class);
        $httpClientMock->method('get')->willReturn($responseMock);

        $responseMock->method('getBody')->willReturn($streamMock);
        $streamMock->method('getContents')->willReturn('{"rates": {"USD": 1.2345}}');

        $exchangeRateApiClient = new ExchangeRateApiClient($httpClientMock);

        $this->assertSame(1.2345, $exchangeRateApiClient->getRateByCurrency('USD'));
    }

    public function testGetRateByCurrencyException()
    {
        $currency = 'USD';
        $url = 'https://api.exchangeratesapi.io/latest';

        $this->httpClient->method('get')
            ->with($url)
            ->willThrowException(new RequestException('Error', new Request('GET', $url)));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error fetching exchange rates');

        $this->client->getRateByCurrency($currency);
    }
}