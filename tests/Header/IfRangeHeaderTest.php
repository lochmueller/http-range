<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Tests\Header;

use Lochmueller\HttpRange\Header\IfRangeHeader;
use Lochmueller\HttpRange\Tests\AbstractUnitTest;
use PHPUnit\Framework\Attributes\TestWith;

class IfRangeHeaderTest extends AbstractUnitTest
{
    #[
        TestWith(['W/"xyzzy"']),
        TestWith(['Wed, 21 Oct 2015 07:28:00 GMT'])
    ]
    public function testValidIfRangeHeader(string $header): void
    {
        $ifRangeHeader = new IfRangeHeader($header);

        self::assertTrue($ifRangeHeader->valid());
    }
}
