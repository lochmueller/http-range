<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Tests;

use Lochmueller\HttpRange\HttpRangeMiddleware;
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
                $text = $this->getFixtureText();

                return $response->withBody($text->getStream(0, $text->getSize()));
            });

        $middleware = new HttpRangeMiddleware();
        $result = $middleware->process($request, $handler);

        self::assertTrue($result->hasHeader('Content-Range'));
        self::assertEquals(10, \strlen($result->getBody()->getContents()));
    }
}
