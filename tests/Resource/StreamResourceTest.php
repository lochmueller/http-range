<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Tests\Resource;

use Lochmueller\HttpRange\Resource\StreamResource;
use Lochmueller\HttpRange\Tests\AbstractUnitTest;
use Nyholm\Psr7\Stream;

class StreamResourceTest extends AbstractUnitTest
{
    public function testValidCallsOnStreamResource(): void
    {
        $resource = fopen('php://memory', 'r+');
        fwrite($resource, 'Das ist ein Test');
        rewind($resource);

        $streamResource = new StreamResource(new Stream($resource));

        self::assertEquals('Das', $streamResource->getStream(0, 3)->getContents());
        self::assertEquals(16, $streamResource->getSize());
    }
}
