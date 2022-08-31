<?php

namespace Hyde\Framework\Testing\Feature\Commands;

use Hyde\Framework\Hyde;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Framework\Commands\HydeBuildSitemapCommand
 * @covers \Hyde\Framework\Actions\PostBuildTasks\GenerateSitemap
 */
class HydeBuildSitemapCommandTest extends TestCase
{
    public function test_sitemap_is_generated_when_conditions_are_met()
    {
        config(['site.url' => 'https://example.com']);
        config(['site.generate_sitemap' => true]);

        unlinkIfExists(Hyde::path('_site/sitemap.xml'));
        $this->artisan('build:sitemap')
            ->assertExitCode(0);

        $this->assertFileExists(Hyde::path('_site/sitemap.xml'));
        unlink(Hyde::path('_site/sitemap.xml'));
    }
}
