<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange;

use Lochmueller\HttpRange\Header\ContentLengthHeader;
use Lochmueller\HttpRange\Header\RangeHeader;
use Lochmueller\HttpRange\Stream\MultipartStream;
use Lochmueller\HttpRange\Stream\RangeWrapperStream;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Http\Range\Unit\UnitRangeInterface;

class HttpRangeRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        protected StreamInterface $stream,
        protected ResponseFactoryInterface $responseFactory,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(200);
        $response = $response
            ->withStatus(200)
            ->withHeader(ContentLengthHeader::NAME, (string) $this->stream->getSize())
            ->withHeader('Accept-Ranges', 'bytes');

        if ('HEAD' === $request->getMethod()) {
            return $response;
        }

        if ('GET' !== $request->getMethod()) {
            return $response->withoutHeader('Accept-Ranges');
        }

        $rangeHeader = new RangeHeader((string) $request->getHeaderLine(RangeHeader::NAME));
        $rangeHeader->setRequestAndTotalSize($request, $this->stream->getSize());
        if (!$rangeHeader->valid()) {
            return $response->withBody($this->stream);
        }
        $ranges = $rangeHeader->getRanges();

        $response = $response->withStatus(206);
        if (1 === $ranges->count()) {
            // Single Range

            /** @var UnitRangeInterface $rangeValue */
            $rangeValue = $ranges->first();
            $innerStream = new RangeWrapperStream($this->stream, $rangeValue->getStart(), $rangeValue->getLength());

            return $response->withHeader('Content-Range', $innerStream->getContentRangeHeader())
                ->withHeader(ContentLengthHeader::NAME, (string) $innerStream->getSize())
                ->withBody($innerStream);
        }

        // Multi Range
        $stream = new MultipartStream();

        foreach ($ranges as $rangeValue) {
            $innerStream = new RangeWrapperStream($this->stream, $rangeValue->getStart(), $rangeValue->getLength());
            $stream->addStream(
                $innerStream,
                ['Content-Range' => $innerStream->getContentRangeHeader()]
            );
        }

        return $response->withHeader('Content-Type', 'multipart/byteranges; boundary=' . $stream->getBoundary())
            ->withBody($stream);
    }
}
