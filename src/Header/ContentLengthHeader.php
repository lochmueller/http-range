<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Header;

class ContentLengthHeader implements HeaderInterace
{
    public const NAME = 'Content-Length';

    public function __construct(protected string $content)
    {
    }

    public function valid(): bool
    {
        return ((int) $this->content) > 0;
    }

    public function get(): string
    {
        return (string) $this->content;
    }
}
