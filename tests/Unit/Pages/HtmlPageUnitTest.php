<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit\Pages;

use Hyde\Hyde;
use Hyde\Pages\HtmlPage;
use Hyde\Support\Models\Route;
use Hyde\Markdown\Models\FrontMatter;
use Hyde\Foundation\Kernel\PageCollection;
use Hyde\Testing\Common\BaseHydePageUnitTest;
use Hyde\Framework\Features\Metadata\PageMetadataBag;
use Hyde\Framework\Factories\Concerns\CoreDataObject;

/**
 * @covers \Hyde\Pages\HtmlPage
 */
class HtmlPageUnitTest extends BaseHydePageUnitTest
{
    public function testSourceDirectory()
    {
        $this->assertSame(
            '_pages',
            HtmlPage::sourceDirectory()
        );
    }

    public function testOutputDirectory()
    {
        $this->assertSame(
            '',
            HtmlPage::outputDirectory()
        );
    }

    public function testBaseRouteKey()
    {
        $this->assertSame(
            '',
            HtmlPage::baseRouteKey()
        );
    }

    public function testFileExtension()
    {
        $this->assertSame(
            '.html',
            HtmlPage::fileExtension()
        );
    }

    public function testSourcePath()
    {
        $this->assertSame(
            '_pages/hello-world.html',
            HtmlPage::sourcePath('hello-world')
        );
    }

    public function testOutputPath()
    {
        $this->assertSame(
            'hello-world.html',
            HtmlPage::outputPath('hello-world')
        );
    }

    public function testPath()
    {
        $this->assertSame(
            Hyde::path('_pages/hello-world.html'),
            HtmlPage::path('hello-world.html')
        );
    }

    public function testGetSourcePath()
    {
        $this->assertSame(
            '_pages/hello-world.html',
            (new HtmlPage('hello-world'))->getSourcePath()
        );
    }

    public function testGetOutputPath()
    {
        $this->assertSame(
            'hello-world.html',
            (new HtmlPage('hello-world'))->getOutputPath()
        );
    }

    public function testGetLink()
    {
        $this->assertSame(
            'hello-world.html',
            (new HtmlPage('hello-world'))->getLink()
        );
    }

    public function testMake()
    {
        $this->assertEquals(HtmlPage::make(), new HtmlPage());
    }

    public function testMakeWithData()
    {
        $this->assertEquals(
            HtmlPage::make('foo', ['foo' => 'bar']),
            new HtmlPage('foo', ['foo' => 'bar'])
        );
    }

    public function testShowInNavigation()
    {
        $this->assertTrue((new HtmlPage())->showInNavigation());
    }

    public function testNavigationMenuPriority()
    {
        $this->assertSame(999, (new HtmlPage())->navigationMenuPriority());
    }

    public function testNavigationMenuLabel()
    {
        $this->assertSame('Foo', (new HtmlPage('foo'))->navigationMenuLabel());
    }

    public function testNavigationMenuGroup()
    {
        $this->assertNull((new HtmlPage('foo'))->navigationMenuGroup());
    }

    public function testGetBladeView()
    {
        $this->assertSame('_pages/foo.html', (new HtmlPage('foo'))->getBladeView());
    }

    public function testFiles()
    {
        $this->assertSame([], HtmlPage::files());
    }

    public function testData()
    {
        $this->assertSame('foo', (new HtmlPage('foo'))->data('identifier'));
    }

    public function testGet()
    {
        $this->file(HtmlPage::sourcePath('foo'));
        $this->assertEquals(new HtmlPage('foo'), HtmlPage::get('foo'));
    }

    public function testParse()
    {
        $this->file(HtmlPage::sourcePath('foo'));
        $this->assertInstanceOf(HtmlPage::class, HtmlPage::parse('foo'));
    }

    public function testGetRouteKey()
    {
        $this->assertSame('foo', (new HtmlPage('foo'))->getRouteKey());
    }

    public function testTitle()
    {
        $htmlPage = new HtmlPage('foo');
        $this->assertSame('HydePHP - Foo', $htmlPage->title());
    }

    public function testAll()
    {
        $this->assertInstanceOf(PageCollection::class, HtmlPage::all());
    }

    public function testMetadata()
    {
        $this->assertInstanceOf(PageMetadataBag::class, (new HtmlPage())->metadata());
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(HtmlPage::class, new HtmlPage());
    }

    public function testGetRoute()
    {
        $this->assertInstanceOf(Route::class, (new HtmlPage())->getRoute());
    }

    public function testGetIdentifier()
    {
        $this->assertSame('foo', (new HtmlPage('foo'))->getIdentifier());
    }

    public function testHas()
    {
        $this->assertTrue((new HtmlPage('foo'))->has('identifier'));
    }

    public function testToCoreDataObject()
    {
        $this->assertInstanceOf(CoreDataObject::class, (new HtmlPage('foo'))->toCoreDataObject());
    }

    public function testCompile()
    {
        $this->file('_pages/foo.html');

        $page = new HtmlPage('foo');
        Hyde::shareViewData($page);
        $this->assertIsString(HtmlPage::class, $page->compile());
    }

    public function testMatter()
    {
        $this->assertInstanceOf(FrontMatter::class, (new HtmlPage('404'))->matter());
    }

    public function testGetCanonicalUrl()
    {
        $page = new HtmlPage('foo');
        $this->assertNull($page->getCanonicalUrl());

        self::mockConfig(['hyde.url' => 'https://example.com']);

        $this->assertSame('https://example.com/foo.html', $page->getCanonicalUrl());

        self::mockConfig(['hyde.url' => 'https://example.com', 'hyde.pretty_urls' => true]);

        $this->assertSame('https://example.com/foo', $page->getCanonicalUrl());

        $page = new HtmlPage('foo', ['canonicalUrl' => 'foo']);
        $this->assertSame('foo', $page->getCanonicalUrl());
    }
}
