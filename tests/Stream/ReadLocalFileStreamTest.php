<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Tests\Stream;

use Lochmueller\HttpRange\Tests\AbstractUnitTest;

class ReadLocalFileStreamTest extends AbstractUnitTest
{
    public function testReadLocalFileIsEmitted(): void
    {
        $this->markTestSkipped('Skip, because this test output content');

        /** @phpstan-ignore-next-line */
        $localFile = $this->getFixtureText();

        // First 10 chars
        $localFile->emit(10);

        // Second 10 chars
        $localFile->emit(20);
    }

    public function testReadLocalFile(): void
    {
        $localFile = $this->getFixtureText();

        self::assertEquals(109, $localFile->getSize());
        self::assertEquals('Ich bin ein Text der ein wenig in der Datei steht um zu prüfen, wie sich die verschiedenen Header verhalten.', $localFile->getContents());

        $localFile->seek(10);
        self::assertEquals('n Text der', $localFile->read(10));
    }
}
