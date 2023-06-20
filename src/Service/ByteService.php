<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Service;

class ByteService
{
    public const DEFAULT_BLOCK_SIZE_IN_KILO_BYTE = 64;

    /**
     * @return non-empty-array<int, positive-int>
     */
    public function chuckBytesInBlocks(int $lengthInByte, int $blockSizeInKiloByte = self::DEFAULT_BLOCK_SIZE_IN_KILO_BYTE): array
    {
        $blockSize = 1024 * $blockSizeInKiloByte;
        $completeBlocks = $lengthInByte / $blockSize;

        $result = array_fill(0, (int) floor($completeBlocks), $blockSize);

        $rest = $lengthInByte - array_sum($result);
        if ($rest > 0) {
            $result[] = $rest;
        }

        /* @phpstan-ignore-next-line */
        return $result;
    }
}
