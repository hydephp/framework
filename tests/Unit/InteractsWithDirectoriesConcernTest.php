<?php

namespace Hyde\Testing\Framework\Unit;

use Hyde\Framework\Concerns\InteractsWithDirectories;
use Hyde\Framework\Hyde;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\File;

/**
 * Class InteractsWithDirectoriesConcernTest.
 *
 * @covers \Hyde\Framework\Concerns\InteractsWithDirectories
 */
class InteractsWithDirectoriesConcernTest extends TestCase
{
    use InteractsWithDirectories;

    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(Hyde::path('foo'));
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(Hyde::path('foo'));

        parent::tearDown();
    }

    public function test_needs_directory_creates_the_directory()
    {
        $this->needsDirectory(Hyde::path('foo'));
        $this->assertDirectoryExists(Hyde::path('foo'));
    }

    public function test_needs_directory_creates_the_directory_recursively()
    {
        $this->needsDirectory(Hyde::path('foo/bar/baz'));
        $this->assertDirectoryExists(Hyde::path('foo/bar/baz'));
    }

    public function test_needs_directory_handles_existing_directory()
    {
        $this->needsDirectory(Hyde::path('foo'));
        $this->needsDirectory(Hyde::path('foo'));
        $this->assertDirectoryExists(Hyde::path('foo'));
    }

    public function test_needs_directories_creates_single_directory()
    {
        $this->needsDirectories([Hyde::path('foo')]);
        $this->assertDirectoryExists(Hyde::path('foo'));
    }

    public function test_needs_directories_creates_multiple_directories()
    {
        $this->needsDirectories([Hyde::path('foo'), Hyde::path('bar')]);
        $this->assertDirectoryExists(Hyde::path('foo'));
        $this->assertDirectoryExists(Hyde::path('bar'));
    }
}
