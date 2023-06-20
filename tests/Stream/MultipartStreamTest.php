<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Tests\Stream;

use Lochmueller\HttpRange\Stream\MultipartStream;
use Lochmueller\HttpRange\Stream\RangeWrapperStream;
use Lochmueller\HttpRange\Tests\AbstractUnitTest;

class MultipartStreamTest extends AbstractUnitTest
{
    public function testMultipartStream(): void
    {
        $stream = new MultipartStream();

        $stream->addStream($this->getFixtureText());
        $stream->addStream(new RangeWrapperStream($this->getFixtureText(), 5, 5));

        // Stream based methods
        self::assertEquals(245, $stream->getSize());

        // Regular Methods
        self::assertNotEmpty($stream->getBoundary());
        self::assertFalse($stream->isWritable());
        self::assertFalse($stream->isSeekable());
        self::assertTrue($stream->isReadable());
        self::assertNull($stream->detach());
        self::assertEquals(0, $stream->tell());
        self::assertFalse($stream->eof());
        self::assertEmpty($stream->read(99));
        self::assertNull($stream->getMetadata());
    }
}
