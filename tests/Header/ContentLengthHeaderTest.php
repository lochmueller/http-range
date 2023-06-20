<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Tests\Header;

use Lochmueller\HttpRange\Header\ContentLengthHeader;
use Lochmueller\HttpRange\Tests\AbstractUnitTest;

class ContentLengthHeaderTest extends AbstractUnitTest
{
    public function testValid(): void
    {
        $contentLengthHeader = new ContentLengthHeader('99');

        self::assertTrue($contentLengthHeader->valid());
        self::assertEquals('99', $contentLengthHeader->get());
    }
}
