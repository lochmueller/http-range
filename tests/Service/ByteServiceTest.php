<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Tests\Service;

use Lochmueller\HttpRange\Service\ByteService;
use Lochmueller\HttpRange\Tests\AbstractUnitTest;
use PHPUnit\Framework\Attributes\DataProvider;

class ByteServiceTest extends AbstractUnitTest
{
    /** @phpstan-ignore-next-line */
    #[DataProvider('chuckDataProvider')]
    public function testChuncksForByteRange(int $lengthInByte, int $blockSizeInKiloByte, array $result): void
    {
        $byteServer = new ByteService();
        self::assertEquals($result, $byteServer->chuckBytesInBlocks($lengthInByte, $blockSizeInKiloByte));
    }

    /** @phpstan-ignore-next-line */
    public static function chuckDataProvider(): iterable
    {
        yield 'Valid length with one block' => [
            150,
            1,
            [
                150,
            ],
        ];

        yield 'Valid length with two block' => [
            1500,
            1,
            [
                1024,
                476,
            ],
        ];
    }
}
