<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Header;

class ETagHeader implements HeaderInterace
{
    public const NAME = 'ETag';

    protected const REGEX = '/\x57\x2f\"[\x21\x23-\x7e]{1,}[?=\"]/';

    public function __construct(protected string $content)
    {
    }

    public function valid(): bool
    {
        return (bool) preg_match(self::REGEX, $this->content);
    }

    public function get(): string
    {
        return $this->content;
    }
}
