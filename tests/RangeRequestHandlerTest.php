<?php

namespace Lochmueller\HttpRange\Tests;

use Lochmueller\HttpRange\Resource\LocalResourceResource;
use Lochmueller\HttpRange\RangeRequestHandler;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RangeRequestHandlerTest extends TestCase
{
    public function testInvalidFilenameRequest()
    {
        $this->expectException(\Lochmueller\HttpRange\Resource\Exception\LocalFileNotFoundException::class);

        $request = new ServerRequest('GET', '/');

        $filePath = __DIR__ . '/fixtures/222-200x300-not-exists.jpg';

        $handler = new RangeRequestHandler(new LocalResourceResource($filePath));

        $handler->handle($request);
    }

    public function testHeadRequestWithValidRange()
    {
        $request = new ServerRequest('HEAD', '/');

        $filePath = __DIR__ . '/fixtures/222-200x300.jpg';
        $handler = new RangeRequestHandler(new LocalResourceResource($filePath));

        $response = $handler->handle($request);

        static::assertTrue($response->hasHeader('Content-Length'));
        static::assertTrue($response->hasHeader('Accept-Ranges'));
    }

    public function testGetRequestWithoutRangeInformation()
    {
        $request = new ServerRequest('GET', '/');

        $filePath = __DIR__ . '/fixtures/222-200x300.jpg';
        $handler = new RangeRequestHandler(new LocalResourceResource($filePath));

        $response = $handler->handle($request);

        static::assertEquals(200, $response->getStatusCode());
        static::assertTrue($response->hasHeader('Accept-Ranges'));
    }

    public function testGetRequestWithRangeInformation()
    {
        $request = new ServerRequest('GET', '/', [
            'Range' => 'bytes=0-199',
        ]);

        $filePath = __DIR__ . '/fixtures/222-200x300.jpg';
        $handler = new RangeRequestHandler(new LocalResourceResource($filePath));

        $response = $handler->handle($request);

        static::assertEquals(206, $response->getStatusCode());
        static::assertTrue($response->hasHeader('Accept-Ranges'));
        static::assertTrue($response->hasHeader('Content-Length'));
        static::assertEquals(200, $response->getHeaderLine('Content-Length'));
    }

    public function testGetRequestWithMultiRangeInformation()
    {
        $request = new ServerRequest('GET', '/', [
            'Range' => 'bytes=0-199,210-250',
        ]);

        $filePath = __DIR__ . '/fixtures/222-200x300.jpg';
        $handler = new RangeRequestHandler(new LocalResourceResource($filePath));

        $response = $handler->handle($request);

        static::assertEquals(206, $response->getStatusCode());
        static::assertTrue($response->hasHeader('Accept-Ranges'));
        static::assertTrue($response->hasHeader('Content-Length'));
        static::assertEquals(11092, $response->getHeaderLine('Content-Length'));
    }

}
