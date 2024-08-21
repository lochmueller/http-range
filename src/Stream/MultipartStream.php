<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Stream;

use Lochmueller\HttpRange\Header\ContentLengthHeader;
use Lochmueller\HttpRange\Stream\Exception\StreamNotWritableException;
use Psr\Http\Message\StreamInterface;

class MultipartStream implements StreamInterface, EmitStreamInterface
{
    /** @phpstan-ignore-next-line */
    protected array $streams = [];

    public function __construct(protected ?string $boundary = null) {}

    public function close(): void {}

    /**
     * @param array<string, string> $headers
     */
    public function addStream(StreamInterface $stream, array $headers = []): void
    {
        if (!$this->hasHeader($headers, ContentLengthHeader::NAME)) {
            if ($length = $stream->getSize()) {
                $headers[ContentLengthHeader::NAME] = (string) $length;
            }
        }

        $this->streams[] = [
            'stream' => $stream,
            'headers' => $headers,
        ];
    }

    public function getBoundary(): string
    {
        if (null === $this->boundary) {
            $this->boundary = uniqid('', true);
        }

        return $this->boundary;
    }

    public function detach()
    {
        return null;
    }

    public function tell(): int
    {
        return 0;
    }

    public function eof(): bool
    {
        return false;
    }

    public function isSeekable(): bool
    {
        return false;
    }

    public function seek(int $offset, int $whence = \SEEK_SET): void {}

    public function rewind(): void {}

    public function isWritable(): bool
    {
        return false;
    }

    public function write(string $string): int
    {
        throw new StreamNotWritableException();
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function read(int $length): string
    {
        return '';
    }

    public function getContents(): string
    {
        return (string) $this;
    }

    public function getMetadata(?string $key = null)
    {
        return null;
    }

    /**
     * @param array<string, string> $headers
     */
    private function getHeaders(array $headers): string
    {
        $str = '';
        foreach ($headers as $key => $value) {
            $str .= \sprintf("%s: %s\r\n", $key, $value);
        }

        return $str;
    }

    public function __toString(): string
    {
        return $this->getStringRepresentation();
    }

    public function getSize(): int
    {
        $sizes = [
            \strlen($this->getStringRepresentation(false)),
        ];
        foreach ($this->streams as $data) {
            $sizes[] = $data['stream']->getSize();
        }

        return (int) array_sum($sizes);
    }

    public function emit(?int $length = null): void
    {
        foreach ($this->streams as $data) {
            echo "--{$this->getBoundary()}\r\n" . $this->getHeaders($data['headers']) . "\r\n";
            if ($data['stream'] instanceof EmitStreamInterface) {
                $data['stream']->emit();
            } else {
                echo (string) $data['stream'];
            }
            echo "\r\n";
        }
        echo "--{$this->getBoundary()}--\r\n";
    }

    protected function getStringRepresentation(bool $inclStreams = true): string
    {
        $output = '';
        foreach ($this->streams as $data) {
            $output .= "--{$this->getBoundary()}\r\n" . $this->getHeaders($data['headers']) . "\r\n";
            if ($inclStreams) {
                $output .= (string) $data['stream'];
            }
            $output .= "\r\n";
        }
        $output .= "--{$this->getBoundary()}--\r\n";

        return $output;
    }

    /**
     * @param array<string, string> $headers
     */
    private function hasHeader(array $headers, string $key): bool
    {
        $lowercaseHeader = strtolower($key);
        foreach ($headers as $k => $v) {
            if (strtolower($k) === $lowercaseHeader) {
                return true;
            }
        }

        return false;
    }
}
