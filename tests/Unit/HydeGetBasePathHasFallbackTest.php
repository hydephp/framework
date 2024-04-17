<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit;

use Hyde\Hyde;
use Hyde\Testing\UnitTestCase;

/**
 * @covers \Hyde\Foundation\HydeKernel::getBasePath
 */
class HydeGetBasePathHasFallbackTest extends UnitTestCase
{
    protected static bool $needsKernel = true;

    public function testHydeGetBasePathFallsBackToCurrentWorkingDirectory()
    {
        $mock = new class extends Hyde
        {
            public static string $basePath;
        };

        $this->assertSame(getcwd(), $mock::getBasePath());
    }
}
