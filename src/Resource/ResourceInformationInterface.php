<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Resource;

use Psr\Http\Message\StreamInterface;

interface ResourceInformationInterface
{
    /**
     * Return the size of the resource.
     * If the size is unknown return null.
     */
    public function getSize(): ?int;

    /**
     * Return the resource as string starting and the given
     * start point (in bytes) with the given length (not end).
     */
    public function getContent(int $start, int $length): string;

    /**
     * Same as getContent but as StreamInterface.
     *
     * @see self::getContent
     */
    public function getStream(int $start, int $length): StreamInterface;
}
