<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange;

use Lochmueller\HttpRange\Header\ETagHeader;
use Lochmueller\HttpRange\Header\IfRangeHeader;
use Lochmueller\HttpRange\Header\LastModifiedHeader;
use Lochmueller\HttpRange\Header\RangeHeader;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HttpRangeMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if ($request->hasHeader(IfRangeHeader::NAME) && !$this->isValidIfRangeCondition($request, $response)) {
            $request->withoutHeader(RangeHeader::NAME);
        }

        $handler = new HttpRangeRequestHandler($response->getBody());
        $internalResponse = $handler->handle($request);

        foreach ($internalResponse->getHeaders() as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response->withBody($internalResponse->getBody())->withStatus($internalResponse->getStatusCode());
    }

    protected function isValidIfRangeCondition(ServerRequestInterface $request, ResponseInterface $response): bool
    {
        $ifRangeHeader = new IfRangeHeader($request->getHeaderLine(IfRangeHeader::NAME));
        if (!$ifRangeHeader->valid()) {
            return false;
        }
        $result = $ifRangeHeader->get();

        if ($result instanceof ETagHeader) {
            return (new ETagHeader($response->getHeaderLine(ETagHeader::NAME)))->get() === $result->get();
        } elseif ($result instanceof LastModifiedHeader) {
            $responseHeader = new LastModifiedHeader($response->getHeaderLine(LastModifiedHeader::NAME));
            if (!$responseHeader->valid()) {
                return false;
            }

            return $responseHeader->get()->getTimestamp() === $result->get()->getTimestamp();
        }

        return false;
    }
}
