<?php

namespace Hyde\Framework\Testing\Feature\Commands;

use Hyde\Framework\Actions\PostBuildTasks\GenerateSearch;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\StaticPageBuilder;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Framework\Commands\HydeBuildSearchCommand
 * @covers \Hyde\Framework\Actions\PostBuildTasks\GenerateSearch
 */
class HydeBuildSearchCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        unlinkIfExists(Hyde::path('_site/docs/search.json'));
        unlinkIfExists(Hyde::path('_site/docs/search.html'));
    }

    protected function tearDown(): void
    {
        unlinkIfExists(Hyde::path('_site/docs/search.html'));
        unlinkIfExists(Hyde::path('_site/docs/search.json'));
        GenerateSearch::$guesstimationFactor = 52.5;
        parent::tearDown();
    }

    public function test_it_creates_the_search_json_file()
    {
        $this->artisan('build:search')
            ->assertExitCode(0);

        $this->assertFileExists(Hyde::path('_site/docs/search.json'));
    }

    public function test_it_creates_the_search_page()
    {
        $this->artisan('build:search')
            ->assertExitCode(0);

        $this->assertFileExists(Hyde::path('_site/docs/search.html'));
    }

    public function test_it_does_not_create_the_search_page_if_disabled()
    {
        config(['docs.create_search_page' => false]);
        $this->artisan('build:search')
            ->assertExitCode(0);

        $this->assertFileDoesNotExist(Hyde::path('_site/docs/search.html'));
    }

    public function test_it_does_not_display_the_estimation_message_when_it_is_less_than_1_second()
    {
        GenerateSearch::$guesstimationFactor = 0;

        $this->artisan('build:search')
            ->doesntExpectOutputToContain('> This will take an estimated')
            ->assertExitCode(0);
    }

    public function test_it_displays_the_estimation_message_when_it_is_greater_than_or_equal_to_1_second()
    {
        GenerateSearch::$guesstimationFactor = 1000;
        Hyde::touch(('_docs/foo.md'));
        $this->mockRoute();
        $this->artisan('build:search')
            ->expectsOutput('This will take an estimated 1 seconds. Terminal may seem non-responsive.')
            ->assertExitCode(0);
        unlink(Hyde::path('_docs/foo.md'));
    }

    public function test_search_files_can_be_generated_for_custom_docs_output_directory()
    {
        DocumentationPage::$outputDirectory = 'foo';
        $this->artisan('build:search')
            ->assertExitCode(0);
        $this->assertFileExists(Hyde::path('_site/foo/search.json'));
        $this->assertFileExists(Hyde::path('_site/foo/search.html'));
        unlink(Hyde::path('_site/foo/search.json'));
        unlink(Hyde::path('_site/foo/search.html'));
        rmdir(Hyde::path('_site/foo'));
    }

    public function test_search_files_can_be_generated_for_custom_site_output_directory()
    {
        StaticPageBuilder::$outputPath = Hyde::path('foo');
        $this->artisan('build:search')
            ->assertExitCode(0);
        $this->assertFileExists(Hyde::path('foo/docs/search.json'));
        $this->assertFileExists(Hyde::path('foo/docs/search.html'));
        unlink(Hyde::path('foo/docs/search.json'));
        unlink(Hyde::path('foo/docs/search.html'));
        rmdir(Hyde::path('foo/docs'));
        rmdir(Hyde::path('foo'));
    }

    public function test_search_files_can_be_generated_for_custom_site_and_docs_output_directories()
    {
        DocumentationPage::$outputDirectory = 'foo';
        StaticPageBuilder::$outputPath = Hyde::path('bar');
        $this->artisan('build:search')
            ->assertExitCode(0);
        $this->assertFileExists(Hyde::path('bar/foo/search.json'));
        $this->assertFileExists(Hyde::path('bar/foo/search.html'));
        unlink(Hyde::path('bar/foo/search.json'));
        unlink(Hyde::path('bar/foo/search.html'));
        rmdir(Hyde::path('bar/foo'));
        rmdir(Hyde::path('bar'));
    }

    public function test_search_files_can_be_generated_for_custom_site_and_nested_docs_output_directories()
    {
        DocumentationPage::$outputDirectory = 'foo';
        StaticPageBuilder::$outputPath = Hyde::path('bar/baz');
        $this->artisan('build:search')
            ->assertExitCode(0);
        $this->assertFileExists(Hyde::path('bar/baz/foo/search.json'));
        $this->assertFileExists(Hyde::path('bar/baz/foo/search.html'));
        unlink(Hyde::path('bar/baz/foo/search.json'));
        unlink(Hyde::path('bar/baz/foo/search.html'));
        rmdir(Hyde::path('bar/baz/foo'));
        rmdir(Hyde::path('bar/baz'));
        rmdir(Hyde::path('bar'));
    }
}
