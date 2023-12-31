<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Tests;

use Lochmueller\HttpRange\HttpRangeRequestHandler;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;

class HttpRangeRequestHandlerTest extends AbstractUnitTest
{
    public function testHeadRequestWithValidRange(): void
    {
        $request = new ServerRequest('HEAD', '/');

        $handler = new HttpRangeRequestHandler($this->getFixtureImage(), new Psr17Factory());

        $response = $handler->handle($request);

        static::assertTrue($response->hasHeader('Content-Length'));
        static::assertTrue($response->hasHeader('Accept-Ranges'));
    }

    public function testGetRequestWithoutRangeInformation(): void
    {
        $request = new ServerRequest('GET', '/');

        $handler = new HttpRangeRequestHandler($this->getFixtureImage(), new Psr17Factory());

        $response = $handler->handle($request);

        static::assertEquals(200, $response->getStatusCode());
        static::assertTrue($response->hasHeader('Accept-Ranges'));
    }

    public function testGetRequestWithRangeInformation(): void
    {
        $request = new ServerRequest('GET', '/', [
            'Range' => 'bytes=0-199',
        ]);

        $handler = new HttpRangeRequestHandler($this->getFixtureImage(), new Psr17Factory());

        $response = $handler->handle($request);

        static::assertEquals(206, $response->getStatusCode());
        static::assertTrue($response->hasHeader('Accept-Ranges'));
        static::assertTrue($response->hasHeader('Content-Length'));
        static::assertEquals(200, $response->getHeaderLine('Content-Length'));
    }

    public function testGetRequestWithMultiRangeInformation(): void
    {
        $request = new ServerRequest('GET', '/', [
            'Range' => 'bytes=0-9,15-19',
        ]);

        $handler = new HttpRangeRequestHandler($this->getFixtureText(), new Psr17Factory());

        $response = $handler->handle($request);

        static::assertEquals(206, $response->getStatusCode());
        static::assertTrue($response->hasHeader('Accept-Ranges'));
        static::assertTrue($response->hasHeader('Content-Length'));
        static::assertEquals(109, $response->getHeaderLine('Content-Length'));
    }

    public function testGetRequestWithSingleRangeStartInformation(): void
    {
        $request = new ServerRequest('GET', '/', [
            'Range' => 'bytes=2000-',
        ]);

        $handler = new HttpRangeRequestHandler($this->getFixtureImage(), new Psr17Factory());

        $response = $handler->handle($request);

        static::assertEquals(206, $response->getStatusCode());
        static::assertTrue($response->hasHeader('Accept-Ranges'));
        static::assertTrue($response->hasHeader('Content-Length'));
        static::assertEquals(9092, $response->getHeaderLine('Content-Length'));
    }
}
