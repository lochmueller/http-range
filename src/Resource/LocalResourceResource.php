<?php

namespace Lochmueller\HttpRange\Resource;

use Lochmueller\HttpRange\Resource\Exception\LocalFileNotFoundException;
use Lochmueller\HttpRange\Resource\Exception\LocalFileNotReadableException;

class LocalResourceResource implements ResourceInformationInterface
{
    /**
     * @throws LocalFileNotFoundException
     * @throws LocalFileNotReadableException
     */
    public function __construct(protected string $absoluteFilePath)
    {
        if (!is_file($this->absoluteFilePath)) {
            throw new LocalFileNotFoundException($this->absoluteFilePath . ' was not found.');
        }
        if (!is_readable($this->absoluteFilePath)) {
            throw new LocalFileNotReadableException($this->absoluteFilePath . ' is not readble.');
        }
    }

    public function getFilesize(): int
    {
        return filesize($this->absoluteFilePath);
    }

    public function getContent(int $start, int $end): string
    {
        return file_get_contents(
            filename: $this->absoluteFilePath,
            offset: $start,
            length: $end
        );
    }

    public function getResource(int $start, int $end)
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $this->getContent($start, $end));
        rewind($stream);
        return $stream;
    }

}
