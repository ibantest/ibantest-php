<?php

declare(strict_types=1);

namespace Ibantest\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Ibantest\Ibantest;
use PHPUnit\Framework\TestCase;

final class IbantestTest extends TestCase
{
    public function testValidateIbanBuildsEncodedPath(): void
    {
        $history = [];
        $api = $this->createApiWithHistory($history, [
            new Response(200, [], '{"ok":true}'),
        ]);

        $result = $api->validateIban('DE00 123/45');

        self::assertTrue($result['ok']);
        self::assertSame(
            'https://api.ibantest.com/v1/validate-iban/DE00%20123%2F45',
            (string) $history[0]['request']->getUri()
        );
    }

    public function testCountrySpecificMethodBuildsExpectedPath(): void
    {
        $history = [];
        $api = $this->createApiWithHistory($history, [
            new Response(200, [], '{"ok":true}'),
        ]);

        $api->calculateEsIban('1465', '0092', '1234567890');

        self::assertSame(
            'https://api.ibantest.com/v1/calculate-iban/ES/1465/0092/1234567890',
            (string) $history[0]['request']->getUri()
        );
    }

    public function testClientExceptionReturnsDecodedApiErrorPayload(): void
    {
        $history = [];
        $request = new Request('GET', 'validate-iban/DE00');
        $response = new Response(400, [], '{"message":"Invalid IBAN","errorCode":4001}');

        $api = $this->createApiWithHistory($history, [
            new ClientException('Bad Request', $request, $response),
        ]);

        $result = $api->validateIban('DE00');

        self::assertSame('Invalid IBAN', $result['message']);
        self::assertSame(4001, $result['errorCode']);
    }

    /**
     * @param array<int, array<string, mixed>> $history
     * @param array<int, Response|ClientException> $queue
     */
    private function createApiWithHistory(array &$history, array $queue): Ibantest
    {
        $mock = new MockHandler($queue);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($history));

        $client = new Client([
            'handler' => $handlerStack,
            'base_uri' => 'https://api.ibantest.com/v1/',
        ]);

        $api = new Ibantest(client: $client);
        $api->setToken('test-token');

        return $api;
    }
}
