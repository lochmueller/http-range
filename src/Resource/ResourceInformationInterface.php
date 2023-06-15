<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Resource;

use Psr\Http\Message\StreamInterface;

interface ResourceInformationInterface
{
    public function getSize(): int;

    public function getContent(int $start, int $end): string;

    public function getResource(int $start, int $end);

    public function getStream(int $start, int $end): StreamInterface;
}
