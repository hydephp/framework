<?php

namespace Feature\Services;

use Hyde\Framework\Hyde;
use Hyde\Framework\Services\StarterFileService;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hyde\Framework\Services\StarterFileService
 */
class StarterFileServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach (StarterFileService::$files as $file) {
            unlinkIfExists(Hyde::path($file));
        }
    }

    public function testPublish()
    {
        StarterFileService::publish();

        foreach (StarterFileService::$files as $file) {
            $this->assertFileExists(Hyde::path($file));
        }
    }
}
