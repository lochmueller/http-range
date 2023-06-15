<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Resource;

use Nyholm\Psr7\Stream;
use Psr\Http\Message\StreamInterface;

class StreamResource implements ResourceInformationInterface
{
    public function __construct(protected StreamInterface $stream)
    {
    }

    public function getSize(): int
    {
        return $this->stream->getSize();
    }

    public function getContent(int $start, int $end): string
    {
        $this->stream->seek($start, \SEEK_SET);
        $content = $this->stream->read($end);
        $this->stream->rewind();

        return $content;
    }

    public function getResource(int $start, int $end)
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $this->getContent($start, $end));
        rewind($stream);

        return $stream;
    }

    public function getStream(int $start, int $end): StreamInterface
    {
        return new Stream($this->getResource($start, $end));
    }
}
