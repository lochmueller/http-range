<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Stream;

use Psr\Http\Message\StreamInterface;

interface EmitStreamInterface extends StreamInterface
{
    /**
     * Same as read, but the content is emitted to the client, to reduce the memory
     * print with large files. If the length is empty, the complete stream is emitted.
     *
     * @param positive-int|null $length
     */
    public function emit(?int $length = null): void;
}
