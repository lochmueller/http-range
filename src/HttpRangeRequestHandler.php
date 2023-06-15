<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange;

use GuzzleHttp\Psr7\HttpFactory;
use Http\Message\MultipartStream\MultipartStreamBuilder;
use Lochmueller\HttpRange\Resource\ResourceInformationInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Http\Range\Exception\NoRangeException;
use Ramsey\Http\Range\Range;
use Ramsey\Http\Range\Unit\UnitRangeInterface;

class HttpRangeRequestHandler implements RequestHandlerInterface
{
    public function __construct(protected ResourceInformationInterface $resource)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $response = $response
            ->withStatus(200)
            ->withHeader('Content-Length', $this->resource->getSize())
            ->withHeader('Accept-Ranges', 'bytes');

        if ('HEAD' === $request->getMethod()) {
            return $response;
        }

        $range = new Range($request, $this->resource->getSize());

        try {
            $ranges = $range->getUnit()->getRanges();

            $response = $response->withStatus(206);
            if (1 === $ranges->count()) {
                /** @var UnitRangeInterface $rangeValue */
                $rangeValue = $ranges->first();

                return $response->withHeader('Content-Range', 'bytes '.sprintf('%s-%s/%s', $rangeValue->getStart(), $rangeValue->getLength(), $this->resource->getSize()))
                    ->withHeader('Content-Length', $rangeValue->getLength() - $rangeValue->getStart())
                    ->withBody(new Stream($this->resource->getResource($rangeValue->getStart(), $rangeValue->getLength())));
            } elseif ($ranges->count() > 1) {
                $builder = new MultipartStreamBuilder(new Psr17Factory());

                foreach ($ranges as $rangeValue) {
                    /** @var UnitRangeInterface $rangeValue */
                    $stream = $this->resource->getStream($rangeValue->getStart(), $rangeValue->getLength());
                    $builder->addData(
                        $stream,
                        [
                            'Content-Range' => 'bytes '.sprintf('%s-%s/%s', $rangeValue->getStart(), $rangeValue->getLength(), $this->resource->getSize()),
                            'Content-Length' => $stream->getSize(),
                        ]
                    );
                }

                $response->withHeader('Content-Type', 'multipart/byteranges; boundary='.$builder->getBoundary())
                    ->withBody($builder->build());

                return $response;
            }
        } catch (NoRangeException $e) {
            // No range deliver complete file
        }

        return $response->withBody(new Stream($this->resource->getResource(0, $this->resource->getSize())));
    }
}
