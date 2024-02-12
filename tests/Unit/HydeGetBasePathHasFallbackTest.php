<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit;

use Hyde\Hyde;
use Hyde\Testing\TestCase;

/**
 * Class HydeGetBasePathHasFallbackTest.
 *
 * @covers \Hyde\Foundation\HydeKernel::getBasePath
 */
class HydeGetBasePathHasFallbackTest extends TestCase
{
    public function testHydeGetBasePathFallsBackToGetcwd()
    {
        $mock = new class extends Hyde
        {
            public static string $basePath;
        };
        $this->assertEquals(getcwd(), $mock::getBasePath());
    }
}
