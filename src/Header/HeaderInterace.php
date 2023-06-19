<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Header;

interface HeaderInterace
{
    public function __construct(string $content);

    public function valid(): bool;

    public function get(): mixed;
}
