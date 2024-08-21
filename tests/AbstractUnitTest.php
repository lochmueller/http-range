<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Tests;

use Lochmueller\HttpRange\Stream\ReadLocalFileStream;
use PHPUnit\Framework\TestCase;

abstract class AbstractUnitTest extends TestCase
{
    protected function getFixtureImage(): ReadLocalFileStream
    {
        return new ReadLocalFileStream($this->getAbsoluteFixturesPath() . '222-200x300.jpg');
    }

    protected function getFixtureText(): ReadLocalFileStream
    {
        return new ReadLocalFileStream($this->getAbsoluteFixturesPath() . 'test-text.txt');
    }

    protected function getFixtureVideo(): ReadLocalFileStream
    {
        return new ReadLocalFileStream($this->getAbsoluteFixturesPath() . 'hugeVideo.mp4');
    }

    private function getAbsoluteFixturesPath(): string
    {
        return __DIR__ . '/fixtures/';
    }
}
