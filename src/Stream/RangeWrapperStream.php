<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Stream;

use Lochmueller\HttpRange\Service\ByteService;
use Lochmueller\HttpRange\Stream\Exception\StreamNotWritableException;
use Psr\Http\Message\StreamInterface;

class RangeWrapperStream implements StreamInterface, EmitStreamInterface
{
    public function __construct(
        protected StreamInterface $stream,
        protected int $start,
        protected ?int $length,
    ) {
        $this->rewind();
    }

    public function __toString(): string
    {
        if ($this->stream->isSeekable()) {
            $this->rewind();

            return $this->stream->read($this->length);
        }
        $content = (string) $this->stream;

        return substr($content, $this->start, $this->length);
    }

    public function close(): void
    {
    }

    public function detach()
    {
        return null;
    }

    public function getSize(): int
    {
        return $this->length;
    }

    public function tell(): int
    {
        return $this->stream->tell() - $this->start;
    }

    public function eof(): bool
    {
        return $this->stream->tell() > $this->maxLength();
    }

    public function isSeekable(): bool
    {
        return $this->stream->isSeekable();
    }

    public function seek(int $offset, int $whence = \SEEK_SET): void
    {
        $this->stream->seek($this->start + $offset, $whence);
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable()
    {
        return false;
    }

    public function write(string $string): int
    {
        throw new StreamNotWritableException();
    }

    public function isReadable()
    {
        return $this->stream->isReadable();
    }

    public function read(int $length): string
    {
        if (($this->stream->tell() + $length) > $this->maxLength()) {
            $length = $this->maxLength() - $this->stream->tell();
        }

        return $this->stream->read($length);
    }

    public function getContents(): string
    {
        return (string) $this;
    }

    public function getMetadata(string $key = null)
    {
        return null;
    }

    protected function maxLength(): int
    {
        return $this->length + $this->start;
    }

    public function emit(int $length = null): void
    {
        if ($this->stream instanceof EmitStreamInterface) {
            $this->stream->emit($length);

            return;
        }

        if (null === $length) {
            $length = $this->getSize() - $this->tell();
        }

        $byteService = new ByteService();
        $selectionBlocks = $byteService->chuckBytesInBlocks($length);

        $this->rewind();
        for ($i = 0; !$this->eof(); ++$i) {
            echo $this->read($selectionBlocks[$i]);
        }
    }
}
