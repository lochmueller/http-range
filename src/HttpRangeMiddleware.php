<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange;

use Lochmueller\HttpRange\Resource\StreamResource;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HttpRangeMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $streamResource = new StreamResource($response->getBody());
        $handler = new HttpRangeRequestHandler($streamResource);
        $internalResponse = $handler->handle($request);

        foreach ($internalResponse->getHeaders() as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response->withBody($internalResponse->getBody());
    }
}
