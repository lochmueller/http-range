<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Header;

class IfRangeHeader implements HeaderInterace
{
    public const NAME = 'If-Range';

    public function __construct(protected string $content) {}

    public function valid(): bool
    {
        return null !== $this->get();
    }

    public function get(): ?HeaderInterace
    {
        $etagHeader = new ETagHeader($this->content);
        if ($etagHeader->valid()) {
            return $etagHeader;
        }

        $lastModifiedHeader = new LastModifiedHeader($this->content);
        if ($lastModifiedHeader->valid()) {
            return $lastModifiedHeader;
        }

        return null;
    }
}
