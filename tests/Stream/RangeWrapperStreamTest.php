<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Tests\Stream;

use Lochmueller\HttpRange\Stream\RangeWrapperStream;
use Lochmueller\HttpRange\Tests\AbstractUnitTest;

class RangeWrapperStreamTest extends AbstractUnitTest
{
    public function testSingleRangeOnTextFile(): void
    {
        $localFile = $this->getFixtureText();

        $range = new RangeWrapperStream($localFile, 10, 16);

        self::assertEquals(16, $range->getSize());
        self::assertEquals('n Text der ein w', $range->getContents());

        $range->seek(5);

        self::assertEquals('t der ein w', $range->read(1000));
    }
}
