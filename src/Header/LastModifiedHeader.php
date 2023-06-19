<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Header;

class LastModifiedHeader implements HeaderInterace
{
    public const NAME = 'Last-Modified';

    public function __construct(protected string $content)
    {
    }

    public function valid(): bool
    {
        return $this->get() instanceof \DateTime;
    }

    public function get(): ?\DateTime
    {
        try {
            $dateTime = \DateTime::createFromFormat(\DateTimeInterface::RFC2822, $this->content);
            if (false === $dateTime) {
                return null;
            }

            return $dateTime;
        } catch (\Exception) {
        }

        return null;
    }
}
