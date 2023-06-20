<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Header;

class RangeHeader implements HeaderInterace
{
    public const NAME = 'Range';

    public function __construct(protected string $content)
    {
    }

    public function valid(): bool
    {
        // Not implement.
        // Foreign lib is used.
        return false;
    }

    public function get(): string
    {
        return $this->content;
    }
}
