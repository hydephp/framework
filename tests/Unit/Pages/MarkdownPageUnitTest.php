<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit\Pages;

use Hyde\Hyde;
use Hyde\Facades\Filesystem;
use Hyde\Pages\MarkdownPage;
use Hyde\Support\Models\Route;
use Hyde\Markdown\Models\Markdown;
use Hyde\Markdown\Models\FrontMatter;
use Hyde\Foundation\Kernel\PageCollection;
use Hyde\Testing\Common\BaseMarkdownPageUnitTest;
use Hyde\Framework\Factories\Concerns\CoreDataObject;
use Hyde\Framework\Features\Metadata\PageMetadataBag;

/**
 * @covers \Hyde\Pages\MarkdownPage
 */
class MarkdownPageUnitTest extends BaseMarkdownPageUnitTest
{
    public function testSourceDirectory()
    {
        $this->assertSame(
            '_pages',
            MarkdownPage::sourceDirectory()
        );
    }

    public function testOutputDirectory()
    {
        $this->assertSame(
            '',
            MarkdownPage::outputDirectory()
        );
    }

    public function testBaseRouteKey()
    {
        $this->assertSame(
            '',
            MarkdownPage::baseRouteKey()
        );
    }

    public function testFileExtension()
    {
        $this->assertSame(
            '.md',
            MarkdownPage::fileExtension()
        );
    }

    public function testSourcePath()
    {
        $this->assertSame(
            '_pages/hello-world.md',
            MarkdownPage::sourcePath('hello-world')
        );
    }

    public function testOutputPath()
    {
        $this->assertSame(
            'hello-world.html',
            MarkdownPage::outputPath('hello-world')
        );
    }

    public function testPath()
    {
        $this->assertSame(
            Hyde::path('_pages/hello-world.md'),
            MarkdownPage::path('hello-world.md')
        );
    }

    public function testGetSourcePath()
    {
        $this->assertSame(
            '_pages/hello-world.md',
            (new MarkdownPage('hello-world'))->getSourcePath()
        );
    }

    public function testGetOutputPath()
    {
        $this->assertSame(
            'hello-world.html',
            (new MarkdownPage('hello-world'))->getOutputPath()
        );
    }

    public function testGetLink()
    {
        $this->assertSame(
            'hello-world.html',
            (new MarkdownPage('hello-world'))->getLink()
        );
    }

    public function testMake()
    {
        $this->assertEquals(MarkdownPage::make(), new MarkdownPage());
    }

    public function testMakeWithData()
    {
        $this->assertEquals(
            MarkdownPage::make('foo', ['foo' => 'bar']),
            new MarkdownPage('foo', ['foo' => 'bar'])
        );
    }

    public function testShowInNavigation()
    {
        $this->assertTrue((new MarkdownPage())->showInNavigation());
    }

    public function testNavigationMenuPriority()
    {
        $this->assertSame(999, (new MarkdownPage())->navigationMenuPriority());
    }

    public function testNavigationMenuLabel()
    {
        $this->assertSame('Foo', (new MarkdownPage('foo'))->navigationMenuLabel());
    }

    public function testNavigationMenuGroup()
    {
        $this->assertNull((new MarkdownPage('foo'))->navigationMenuGroup());
    }

    public function testGetBladeView()
    {
        $this->assertSame('hyde::layouts/page', (new MarkdownPage('foo'))->getBladeView());
    }

    public function testFiles()
    {
        $this->assertSame([], MarkdownPage::files());
    }

    public function testData()
    {
        $this->assertSame('foo', (new MarkdownPage('foo'))->data('identifier'));
    }

    public function testGet()
    {
        $this->file(MarkdownPage::sourcePath('foo'));
        $this->assertEquals(new MarkdownPage('foo'), MarkdownPage::get('foo'));
    }

    public function testParse()
    {
        $this->file(MarkdownPage::sourcePath('foo'));
        $this->assertInstanceOf(MarkdownPage::class, MarkdownPage::parse('foo'));
    }

    public function testGetRouteKey()
    {
        $this->assertSame('foo', (new MarkdownPage('foo'))->getRouteKey());
    }

    public function testTitle()
    {
        $markdownPage = new MarkdownPage('foo');
        $this->assertSame('HydePHP - Foo', $markdownPage->title());
    }

    public function testAll()
    {
        $this->assertInstanceOf(PageCollection::class, MarkdownPage::all());
    }

    public function testMetadata()
    {
        $this->assertInstanceOf(PageMetadataBag::class, (new MarkdownPage())->metadata());
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(MarkdownPage::class, new MarkdownPage());
    }

    public function testGetRoute()
    {
        $this->assertInstanceOf(Route::class, (new MarkdownPage())->getRoute());
    }

    public function testGetIdentifier()
    {
        $this->assertSame('foo', (new MarkdownPage('foo'))->getIdentifier());
    }

    public function testHas()
    {
        $this->assertTrue((new MarkdownPage('foo'))->has('identifier'));
    }

    public function testToCoreDataObject()
    {
        $this->assertInstanceOf(CoreDataObject::class, (new MarkdownPage('foo'))->toCoreDataObject());
    }

    public function testCompile()
    {
        $page = new MarkdownPage('foo');
        Hyde::shareViewData($page);
        $this->assertIsString(MarkdownPage::class, $page->compile());
    }

    public function testMatter()
    {
        $this->assertInstanceOf(FrontMatter::class, (new MarkdownPage('foo'))->matter());
    }

    public function testMarkdown()
    {
        $this->assertInstanceOf(Markdown::class, (new MarkdownPage('foo'))->markdown());
    }

    public function testSave()
    {
        $page = new MarkdownPage('foo');
        $this->assertSame($page, $page->save());
        $this->assertFileExists('_pages/foo.md');
        Filesystem::unlink('_pages/foo.md');
    }

    public function testGetCanonicalUrl()
    {
        $page = new MarkdownPage('foo');
        $this->assertNull($page->getCanonicalUrl());

        self::mockConfig(['hyde.url' => 'https://example.com']);

        $this->assertSame('https://example.com/foo.html', $page->getCanonicalUrl());

        self::mockConfig(['hyde.url' => 'https://example.com', 'hyde.pretty_urls' => true]);

        $this->assertSame('https://example.com/foo', $page->getCanonicalUrl());

        $page = new MarkdownPage('foo', ['canonicalUrl' => 'foo']);
        $this->assertSame('foo', $page->getCanonicalUrl());
    }
}
