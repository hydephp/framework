<?php

namespace Hyde\Framework\Testing\Feature\Commands;

use Hyde\Framework\Hyde;
use Hyde\Testing\TestCase;
use Illuminate\Foundation\PackageManifest;

/**
 * @covers \Hyde\Framework\Commands\HydePackageDiscoverCommand
 */
class HydePackageDiscoverCommandTest extends TestCase
{
    public function test_package_discover_command_can_run()
    {
        $this->artisan('package:discover')->assertExitCode(0);
    }

    public function test_package_discover_command_registers_manifest_path()
    {
        $this->artisan('package:discover')->assertExitCode(0);
        $this->assertEquals(Hyde::path('storage/framework/cache/packages.php'),
            $this->app->make(PackageManifest::class)->manifestPath);
        $this->assertFileExists(Hyde::path('storage/framework/cache/packages.php'));
    }
}
