<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Tests;

use Lochmueller\HttpRange\HttpRangeMiddleware;
use Lochmueller\HttpRange\Stream\EmitStreamInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Server\RequestHandlerInterface;

class HttpRangeMiddlewareTest extends AbstractUnitTest
{
    public function testMiddleware(): void
    {
        $request = new ServerRequest('GET', '/', [
            'Range' => 'bytes=0-9',
        ]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturnCallback(function () {
                $response = new Response();

                return $response->withBody($this->getFixtureText());
            });

        $middleware = new HttpRangeMiddleware(new Psr17Factory());
        $result = $middleware->process($request, $handler);

        self::assertTrue($result->hasHeader('Content-Range'));
        self::assertEquals(10, \strlen($result->getBody()->getContents()));
    }

    public function testMiddlewareWithStartingRange(): void
    {
        $request = new ServerRequest('GET', '/', [
            'Range' => 'bytes=14-',
        ]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturnCallback(function () {
                $response = new Response();

                return $response->withBody($this->getFixtureText());
            });

        $middleware = new HttpRangeMiddleware(new Psr17Factory());
        $result = $middleware->process($request, $handler);

        self::assertTrue($result->hasHeader('Content-Range'));
        self::assertEquals(95, \strlen($result->getBody()->getContents()));
    }

    public function testMiddlewareWithFullRange(): void
    {
        $request = new ServerRequest('GET', '/', [
            'Range' => 'bytes=0-',
        ]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturnCallback(function () {
                $response = new Response();

                return $response->withBody($this->getFixtureText());
            });

        $middleware = new HttpRangeMiddleware(new Psr17Factory());
        $result = $middleware->process($request, $handler);

        self::assertTrue($result->hasHeader('Content-Range'));
        self::assertEquals('bytes 0-108/109', $result->getHeaderLine('Content-Range'));
        self::assertEquals(109, \strlen($result->getBody()->getContents()));
    }

    public function testMemoryUsageOfHugeFiles(): void
    {
        $this->markTestSkipped('Skip, because this test output content');

        /** @phpstan-ignore-next-line */
        $request = new ServerRequest('GET', '/', [
            'Range' => 'bytes=0-104857600',
        ]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturnCallback(function () {
                $response = new Response();

                return $response->withBody($this->getFixtureVideo());
            });

        $middleware = new HttpRangeMiddleware(new Psr17Factory());
        $result = $middleware->process($request, $handler);

        // Call Body rendering
        $body = $result->getBody();
        self::assertInstanceOf(EmitStreamInterface::class, $body);

        $body->emit();

        self::assertTrue($result->hasHeader('Content-Range'));
        $memoryPeak = memory_get_peak_usage(true) / 1024 / 1024;
        $memoryUsage = memory_get_usage(true) / 1024 / 1024;

        self::assertLessThan(15, $memoryPeak);
        self::assertLessThan(15, $memoryUsage);
    }
}
