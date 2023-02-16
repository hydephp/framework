<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit;

use Hyde\Facades\Site;
use Hyde\Framework\HydeServiceProvider;
use Hyde\Framework\Services\RebuildService;
use Hyde\Hyde;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\File;

/**
 * Class BuildOutputDirectoryCanBeChangedTest.
 */
class BuildOutputDirectoryCanBeChangedTest extends TestCase
{
    public function test_site_output_directory_can_be_changed_for_site_builds()
    {
        $this->file('_posts/test-post.md');

        Site::setOutputDirectory('_site/build');

        $this->artisan('build');

        $this->assertFileExists(Hyde::path('_site/build/posts/test-post.html'));
        $this->assertFileExists(Hyde::path('_site/build/media/app.css'));
        $this->assertFileExists(Hyde::path('_site/build/index.html'));

        File::deleteDirectory(Hyde::path('_site/build'));
    }

    public function test_site_output_directory_can_be_changed_in_static_page_builder()
    {
        $this->file('_posts/test-post.md');

        Site::setOutputDirectory('_site/build');

        (new RebuildService('_posts/test-post.md'))->execute();

        $this->assertFileExists(Hyde::path('_site/build/posts/test-post.html'));

        File::deleteDirectory(Hyde::path('_site/build'));
    }

    public function test_output_directory_is_created_if_it_does_not_exist_in_static_page_builder()
    {
        $this->file('_posts/test-post.md');
        File::deleteDirectory(Hyde::path('_site/build/foo'));
        Site::setOutputDirectory('_site/build/foo');
        (new RebuildService('_posts/test-post.md'))->execute();

        $this->assertFileExists(Hyde::path('_site/build/foo/posts/test-post.html'));
        File::deleteDirectory(Hyde::path('_site/build/foo'));
    }

    public function test_site_output_directory_can_be_changed_in_configuration()
    {
        $this->assertEquals('_site', Site::getOutputDirectory());

        config(['hyde.output_directory' => '_site/build']);
        (new HydeServiceProvider($this->app))->register();

        $this->assertEquals('_site/build', Site::getOutputDirectory());

        $this->file('_posts/test-post.md');
        (new RebuildService('_posts/test-post.md'))->execute();
        $this->assertFileExists(Hyde::path('_site/build/posts/test-post.html'));

        File::deleteDirectory(Hyde::path('_site/build'));
    }

    public function test_site_output_directory_path_is_normalized_to_trim_trailing_slashes()
    {
        Site::setOutputDirectory('foo/bar/');
        $this->assertEquals('foo/bar', Site::getOutputDirectory());
    }
}
