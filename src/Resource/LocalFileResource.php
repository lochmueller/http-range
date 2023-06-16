<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Resource;

use Lochmueller\HttpRange\Resource\Exception\LocalFileNotFoundException;
use Lochmueller\HttpRange\Resource\Exception\LocalFileNotReadableException;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\StreamInterface;

class LocalFileResource implements ResourceInformationInterface
{
    /**
     * @throws LocalFileNotFoundException
     * @throws LocalFileNotReadableException
     */
    public function __construct(protected string $absoluteFilePath)
    {
        if (!is_file($this->absoluteFilePath)) {
            throw new LocalFileNotFoundException($this->absoluteFilePath.' was not found.');
        }
        if (!is_readable($this->absoluteFilePath)) {
            throw new LocalFileNotReadableException($this->absoluteFilePath.' is not readble.');
        }
    }

    public function getSize(): int
    {
        return filesize($this->absoluteFilePath);
    }

    public function getContent(int $start, int $length): string
    {
        return file_get_contents(
            filename: $this->absoluteFilePath,
            offset: $start,
            length: $length
        );
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
