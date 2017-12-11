<?php

namespace Http\Adapter\Artax\Test;

use Amp\Artax;
use Amp\Loop;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Adapter\Artax\Client;
use Http\Client\HttpAsyncClient;
use Http\Client\Tests\HttpAsyncClientTest;

class AsyncClientTest extends HttpAsyncClientTest
{
    /** @return HttpAsyncClient */
    protected function createHttpAsyncClient()
    {
        $client = new Artax\DefaultClient();
        $client->setOption(Artax\Client::OP_TRANSFER_TIMEOUT, 1000);

        return new Client($client);
    }

    public function testPromise()
    {
        $content = $response = $exception = null;

        Loop::run(function () use (&$content, &$response, &$exception) {
            $client = $this->createHttpAsyncClient();
            $request = new Request('GET', 'https://httpbin.org/get');

            try {
                $response = yield $client->sendAsyncRequest($request);
                $content = $response->getBody()->getContents();
            } catch (\Throwable $e) {
                $exception = $e;
            }
        });

        if (null !== $exception) {
            throw $exception;
        }

        self::assertInstanceOf(Response::class, $response);
        self::assertNotNull($content);
        self::assertNotEmpty($content);
    }
}