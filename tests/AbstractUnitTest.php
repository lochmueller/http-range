<?php

declare(strict_types=1);

namespace Lochmueller\HttpRange\Tests;

use Lochmueller\HttpRange\Resource\LocalFileResource;
use Lochmueller\HttpRange\Resource\ResourceInformationInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractUnitTest extends TestCase
{
    protected function getFixtureImage(): ResourceInformationInterface
    {
        $filePath = __DIR__.'/fixtures/222-200x300.jpg';

        return new LocalFileResource($filePath);
    }

    protected function getFixtureText(): ResourceInformationInterface
    {
        $filePath = __DIR__.'/fixtures/test-text.txt';

        return new LocalFileResource($filePath);
    }
}
