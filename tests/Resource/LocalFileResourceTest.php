<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Tests\Resource;

use Lochmueller\HttpRange\Resource\Exception\LocalFileNotFoundException;
use Lochmueller\HttpRange\Resource\LocalFileResource;
use Lochmueller\HttpRange\Tests\AbstractUnitTest;

class LocalFileResourceTest extends AbstractUnitTest
{
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
}
