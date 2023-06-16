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

    public function getContent(int $start, int $length): string
    {
        $this->stream->seek($start, \SEEK_SET);
        $content = $this->stream->read($length);
        $this->stream->rewind();

        return $content;
    }

    public function getResource(int $start, int $length)
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $this->getContent($start, $length));
        rewind($stream);

        return $stream;
    }

    public function getStream(int $start, int $length): StreamInterface
    {
        return new Stream($this->getResource($start, $length));
    }
}
