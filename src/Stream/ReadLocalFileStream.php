<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Stream;

use Lochmueller\HttpRange\Service\ByteService;
use Lochmueller\HttpRange\Stream\Exception\LocalFileNotReadableException;
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
        return (string) file_get_contents($this->absoluteFileName);
    }

    public function close(): void
    {
    }

    public function detach()
    {
        $this->absoluteFileName = '';

        return null;
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

    public function write(string $string): int
    {
        throw new StreamNotWritableException();
    }

    public function isReadable(): bool
    {
        return is_readable($this->absoluteFileName);
    }

    /**
     * @param int<0, max> $length
     */
    public function read(int $length): string
    {
        return (string) file_get_contents($this->absoluteFileName, offset: $this->filePointer, length: $length);
    }

    public function getContents(): string
    {
        return (string) $this;
    }

    public function getMetadata(string $key = null)
    {
        return null;
    }

    /**
     * @param int<0, max>|null $length
     *
     * @throws LocalFileNotReadableException
     */
    public function emit(int $length = null): void
    {
        if (null === $length) {
            $length = $this->getSize() - $this->tell();
        }

        $start = $this->tell();
        $fp = fopen($this->absoluteFileName, 'r');
        if (false === $fp) {
            throw new LocalFileNotReadableException();
        }
        fseek($fp, $start);

        $byteService = new ByteService();
        $selectionBlocks = $byteService->chuckBytesInBlocks($length);

        for ($i = 0; !$this->eof(); ++$i) {
            /** @var int<0, max> $internalLength */
            $internalLength = $selectionBlocks[$i];
            echo fread($fp, $internalLength);
            $this->filePointer += $internalLength;
            flush();
        }
        fclose($fp);
    }
}
