<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Resource;

function is_readable($filename)
{
    return \Lochmueller\HttpRange\Tests\Resource\LocalFileResourceTest::$isReadable;
}

namespace Lochmueller\HttpRange\Tests\Resource;

use Lochmueller\HttpRange\Resource\Exception\LocalFileNotFoundException;
use Lochmueller\HttpRange\Resource\Exception\LocalFileNotReadableException;
use Lochmueller\HttpRange\Resource\LocalFileResource;
use Lochmueller\HttpRange\Tests\AbstractUnitTest;

class LocalFileResourceTest extends AbstractUnitTest
{
    public static $isReadable = true;

    public function testInvalidFilename(): void
    {
        $this->expectException(LocalFileNotFoundException::class);

        $filePath = __DIR__.'/../fixtures/222-200x300-not-exists.jpg';
        new LocalFileResource($filePath);
    }

    public function testValidFilesize(): void
    {
        $filePath = __DIR__.'/../fixtures/222-200x300.jpg';
        $file = new LocalFileResource($filePath);

        self::assertEquals(11092, $file->getSize());
    }

    public function testValidSubstream(): void
    {
        $filePath = __DIR__.'/../fixtures/test-text.txt';
        $file = new LocalFileResource($filePath);

        self::assertEquals('Ich bin', $file->getStream(0, 7)->getContents());
    }

    public function testUnreadbaleFile(): void
    {
        self::$isReadable = false;
        $this->expectException(LocalFileNotReadableException::class);

        $filePath = __DIR__.'/../fixtures/test-text.txt';
        $file = new LocalFileResource($filePath);
    }
}
