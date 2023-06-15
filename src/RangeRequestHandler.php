<?php

namespace Lochmueller\HttpRange;

use GuzzleHttp\Psr7\HttpFactory;
use Http\Message\MultipartStream\MultipartStreamBuilder;
use Lochmueller\HttpRange\Resource\ResourceInformationInterface;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Http\Range\Exception\NoRangeException;
use Ramsey\Http\Range\Range;
use Ramsey\Http\Range\Unit\UnitRangeInterface;

class RangeRequestHandler implements RequestHandlerInterface
{
    public function __construct(protected ResourceInformationInterface $file)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $response = $response
            ->withStatus(200)
            ->withHeader('Content-Length', $this->file->getFilesize())
            ->withHeader('Accept-Ranges', 'bytes');

        if ($request->getMethod() === 'HEAD') {
            return $response;
        }

        $range = new Range($request, $this->file->getFilesize());

        try {
            $ranges = $range->getUnit()->getRanges();

            $response = $response->withStatus(206);
            if ($ranges->count() === 1) {

                /** @var UnitRangeInterface $rangeValue */
                $rangeValue = $ranges->first();
                return $response->withHeader('Content-Range', 'bytes ' . sprintf('%s-%s/%s', $rangeValue->getStart(), $rangeValue->getLength(), $this->file->getFilesize()))
                    ->withHeader('Content-Length', $rangeValue->getLength() - $rangeValue->getStart())
                    ->withBody(new Stream($this->file->getResource($rangeValue->getStart(), $rangeValue->getLength())));

            } elseif ($ranges->count() > 1) {


                $builder = new MultipartStreamBuilder(new HttpFactory());

                foreach ($ranges as $rangeValue) {
                    /** @var UnitRangeInterface $rangeValue */
                    $stream = new Stream($this->file->getResource($rangeValue->getStart(), $rangeValue->getLength()));
                    $builder->addData(
                        $stream,
                        [
                            'Content-Range' => 'bytes ' . sprintf('%s-%s/%s', $rangeValue->getStart(), $rangeValue->getLength(), $this->file->getFilesize()),
                            'Content-Length' => $stream->getSize(),
                        ]
                    );
                }

                $response->withHeader('Content-Type', 'multipart/byteranges; boundary=' . $builder->getBoundary())
                    ->withBody($builder->build());
                return $response;
            }
        } catch (NoRangeException $e) {
            // No range deliver complete file
        }

        return $response->withBody(new Stream($this->file->getResource(0, $this->file->getFilesize())));
    }
}
