<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange;

use Lochmueller\HttpRange\Stream\MultipartStream;
use Lochmueller\HttpRange\Stream\RangeWrapperStream;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Http\Range\Exception\NoRangeException;
use Ramsey\Http\Range\Range;
use Ramsey\Http\Range\Unit\UnitRangeInterface;

class HttpRangeRequestHandler implements RequestHandlerInterface
{
    public function __construct(protected StreamInterface $stream)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $response = $response
            ->withStatus(200)
            ->withHeader('Content-Length', (string) $this->stream->getSize())
            ->withHeader('Accept-Ranges', 'bytes');

        if ('HEAD' === $request->getMethod()) {
            return $response;
        }

        if ('GET' !== $request->getMethod()) {
            return $response->withoutHeader('Accept-Ranges');
        }

        $range = new Range($request, $this->stream->getSize());

        try {
            $ranges = $range->getUnit()->getRanges();

            $response = $response->withStatus(206);
            if (1 === $ranges->count()) {
                /** @var UnitRangeInterface $rangeValue */
                $rangeValue = $ranges->first();
                $rangeEnd = $rangeValue->getStart() + $rangeValue->getLength();

                return $response->withHeader('Content-Range', 'bytes '.sprintf('%s-%s/%s', $rangeValue->getStart(), $rangeEnd, $this->stream->getSize()))
                    ->withHeader('Content-Length', $rangeValue->getLength())
                    ->withBody(new RangeWrapperStream($this->stream, $rangeValue->getStart(), $rangeValue->getLength()));
            } elseif ($ranges->count() > 1) {
                $stream = new MultipartStream();

                foreach ($ranges as $rangeValue) {
                    $rangeEnd = $rangeValue->getStart() + $rangeValue->getLength();
                    $stream->addStream(
                        new RangeWrapperStream($this->stream, $rangeValue->getStart(), $rangeValue->getLength()),
                        [
                            'Content-Range' => 'bytes '.sprintf('%s-%s/%s', $rangeValue->getStart(), $rangeEnd, $this->stream->getSize()),
                        ]
                    );
                }

                return $response->withHeader('Content-Type', 'multipart/byteranges; boundary='.$stream->getBoundary())
                    ->withBody($stream);
            }
        } catch (NoRangeException $e) {
            // No range deliver complete file
        }

        return $response->withBody($this->stream);
    }
}
