<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Feature\Commands;

use Hyde\Facades\Filesystem;
use Hyde\Hyde;
use Hyde\Pages\DocumentationPage;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Console\Commands\BuildSearchCommand
 * @covers \Hyde\Framework\Actions\PostBuildTasks\GenerateSearch
 * @covers \Hyde\Framework\Features\Documentation\DocumentationSearchPage
 */
class BuildSearchCommandTest extends TestCase
{
    public function testItCreatesTheSearchJsonFile()
    {
        $this->assertFileDoesNotExist(Hyde::path('_site/docs/search.json'));

        $this->artisan('build:search')->assertExitCode(0);
        $this->assertFileExists(Hyde::path('_site/docs/search.json'));

        Filesystem::unlink('_site/docs/search.json');
        Filesystem::unlink('_site/docs/search.html');
    }

    public function testItCreatesTheSearchPage()
    {
        $this->assertFileDoesNotExist(Hyde::path('_site/docs/search.html'));

        $this->artisan('build:search')->assertExitCode(0);
        $this->assertFileExists(Hyde::path('_site/docs/search.html'));

        Filesystem::unlink('_site/docs/search.json');
        Filesystem::unlink('_site/docs/search.html');
    }

    public function testItDoesNotCreateTheSearchPageIfDisabled()
    {
        config(['docs.create_search_page' => false]);

        $this->artisan('build:search')->assertExitCode(0);
        $this->assertFileDoesNotExist(Hyde::path('_site/docs/search.html'));

        Filesystem::unlink('_site/docs/search.json');
    }

    public function testItDoesNotDisplayTheEstimationMessageWhenItIsLessThan1Second()
    {
        $this->artisan('build:search')
            ->doesntExpectOutputToContain('> This will take an estimated')
            ->assertExitCode(0);

        Filesystem::unlink('_site/docs/search.json');
        Filesystem::unlink('_site/docs/search.html');
    }

    public function testSearchFilesCanBeGeneratedForCustomDocsOutputDirectory()
    {
        DocumentationPage::setOutputDirectory('foo');

        $this->artisan('build:search')->assertExitCode(0);
        $this->assertFileExists(Hyde::path('_site/foo/search.json'));
        $this->assertFileExists(Hyde::path('_site/foo/search.html'));

        Filesystem::deleteDirectory('_site/foo');
    }

    public function testSearchFilesCanBeGeneratedForCustomSiteOutputDirectory()
    {
        Hyde::setOutputDirectory('foo');

        $this->artisan('build:search')->assertExitCode(0);
        $this->assertFileExists(Hyde::path('foo/docs/search.json'));
        $this->assertFileExists(Hyde::path('foo/docs/search.html'));

        Filesystem::deleteDirectory('foo');
    }

    public function testSearchFilesCanBeGeneratedForCustomSiteAndDocsOutputDirectories()
    {
        Hyde::setOutputDirectory('foo');
        DocumentationPage::setOutputDirectory('bar');

        $this->artisan('build:search')->assertExitCode(0);
        $this->assertFileExists(Hyde::path('foo/bar/search.json'));
        $this->assertFileExists(Hyde::path('foo/bar/search.html'));

        Filesystem::deleteDirectory('foo');
    }

    public function testSearchFilesCanBeGeneratedForCustomSiteAndNestedDocsOutputDirectories()
    {
        Hyde::setOutputDirectory('foo/bar');
        DocumentationPage::setOutputDirectory('baz');

        $this->artisan('build:search')->assertExitCode(0);
        $this->assertFileExists(Hyde::path('foo/bar/baz/search.json'));
        $this->assertFileExists(Hyde::path('foo/bar/baz/search.html'));

        Filesystem::deleteDirectory('foo');
    }
}
