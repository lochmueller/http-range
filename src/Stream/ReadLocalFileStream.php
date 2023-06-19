<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Stream;

use Lochmueller\HttpRange\Stream\Exception\StreamNotWritableException;
use Psr\Http\Message\StreamInterface;

class ReadLocalFileStream implements StreamInterface, EmitStreamInterface
{
    protected int $filePointer = 0;

    public function __construct(protected string $absoluteFileName)
    {
    }

    public function __toString(): string
    {
        return file_get_contents($this->absoluteFileName);
    }

    public function close(): void
    {
    }

    public function detach(): void
    {
        $this->absoluteFileName = '';
    }

    public function getSize(): int
    {
        return (int) filesize($this->absoluteFileName);
    }

    public function tell(): int
    {
        return $this->filePointer;
    }

    public function eof(): bool
    {
        return $this->filePointer > $this->getSize();
    }

    public function isSeekable(): bool
    {
        return true;
    }

    public function seek(int $offset, int $whence = \SEEK_SET): void
    {
        if (\SEEK_SET === $whence) {
            $this->filePointer = $offset;
        } elseif (\SEEK_CUR === $whence) {
            $this->filePointer += $offset;
        } elseif (\SEEK_END === $whence) {
            $this->filePointer = $this->getSize() + $offset;
        }
    }

    public function rewind(): void
    {
        $this->filePointer = 0;
    }

    public function isWritable(): bool
    {
        return false;
    }

    public function write(string $string): void
    {
        throw new StreamNotWritableException();
    }

    public function isReadable(): bool
    {
        return is_readable($this->absoluteFileName);
    }

    public function read(int $length): string
    {
        return file_get_contents($this->absoluteFileName, offset: $this->filePointer, length: $length);
    }

    public function getContents(): string
    {
        return (string) $this;
    }

    public function getMetadata(string $key = null): array
    {
        return [];
    }

    public function emit(int $length = null): void
    {
        if (null === $length) {
            $length = $this->getSize() - $this->tell();
        }

        $start = $this->tell();
        $fp = fopen($this->absoluteFileName, 'r');
        $end = $start + $length;
        fseek($fp, $start);

        // Start buffered download (chunks of 64K)
        $buffer = 1024 * 64;
        while (!feof($fp) && ($p = ftell($fp)) <= $end) {
            if ($p + $buffer > $end) {
                // In case we are only outputting a chunk, make sure we do not
                // read past the length
                $buffer = $end - $p + 1;
            }
            echo fread($fp, $buffer);
            $this->filePointer += $buffer;
            flush();
        }

        fclose($fp);
    }
}
