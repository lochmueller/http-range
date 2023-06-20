<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Header;

use Psr\Http\Message\RequestInterface;
use Ramsey\Http\Range\Exception\NoRangeException;
use Ramsey\Http\Range\Range;
use Ramsey\Http\Range\Unit\UnitRangesCollection;

class RangeHeader implements HeaderInterace
{
    public const NAME = 'Range';

    protected RequestInterface $request;

    protected int $totalSize;

    public function __construct(protected string $content)
    {
    }

    public function setRequestAndTotalSize(RequestInterface $request, int $totalSize): void
    {
        $this->request = $request;
        $this->totalSize = $totalSize;
    }

    public function valid(): bool
    {
        try {
            $range = new Range($this->request, $this->totalSize);
            $ranges = $range->getUnit()->getRanges();
            if ($ranges->count() > 0) {
                return true;
            }
        } catch (NoRangeException $e) {
            // No range deliver complete file
        }

        return false;
    }

    public function get(): string
    {
        return $this->content;
    }

    public function getRanges(): ?UnitRangesCollection
    {
        try {
            $range = new Range($this->request, $this->totalSize);
            $ranges = $range->getUnit()->getRanges();
            if ($ranges->count() > 0) {
                return $ranges;
            }
        } catch (NoRangeException $e) {
            // No range deliver complete file
        }

        return null;
    }
}
