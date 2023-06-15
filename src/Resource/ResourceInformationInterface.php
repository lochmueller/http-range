<?php

namespace Lochmueller\HttpRange\Resource;

interface ResourceInformationInterface
{
    public function getFilesize(): int;

    public function getContent(int $start, int $end): string;

    public function getResource(int $start, int $end);

}
