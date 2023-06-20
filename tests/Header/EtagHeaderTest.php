<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Tests\Header;

use Lochmueller\HttpRange\Header\ETagHeader;
use Lochmueller\HttpRange\Tests\AbstractUnitTest;

class EtagHeaderTest extends AbstractUnitTest
{
    public function testInValid(): void
    {
        $eTagHeader = new ETagHeader('invalid header');

        self::assertFalse($eTagHeader->valid());
        self::assertEquals('invalid header', $eTagHeader->get());
    }
}
