<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit\Pages;

use Hyde\Hyde;
use Hyde\Facades\Filesystem;
use Hyde\Pages\MarkdownPost;
use Hyde\Support\Models\Route;
use Hyde\Markdown\Models\Markdown;
use Hyde\Markdown\Models\FrontMatter;
use Hyde\Foundation\Kernel\PageCollection;
use Hyde\Framework\Factories\Concerns\CoreDataObject;
use Hyde\Framework\Features\Metadata\PageMetadataBag;
use Hyde\Framework\Testing\helpers\BaseMarkdownPageUnitTest;

require_once __DIR__.'/../../helpers/BaseMarkdownPageUnitTest.php';

/**
 * @covers \Hyde\Pages\MarkdownPost
 */
class MarkdownPostUnitTest extends BaseMarkdownPageUnitTest
{
    public function testSourceDirectory()
    {
        $this->assertSame(
            '_posts',
            MarkdownPost::sourceDirectory()
        );
    }

    public function testOutputDirectory()
    {
        $this->assertSame(
            'posts',
            MarkdownPost::outputDirectory()
        );
    }

    public function testBaseRouteKey()
    {
        $this->assertSame(
            'posts',
            MarkdownPost::baseRouteKey()
        );
    }

    public function testFileExtension()
    {
        $this->assertSame(
            '.md',
            MarkdownPost::fileExtension()
        );
    }

    public function testSourcePath()
    {
        $this->assertSame(
            '_posts/hello-world.md',
            MarkdownPost::sourcePath('hello-world')
        );
    }

    public function testOutputPath()
    {
        $this->assertSame(
            'posts/hello-world.html',
            MarkdownPost::outputPath('hello-world')
        );
    }

    public function testPath()
    {
        $this->assertSame(
            Hyde::path('_posts/hello-world.md'),
            MarkdownPost::path('hello-world.md')
        );
    }

    public function testGetSourcePath()
    {
        $this->assertSame(
            '_posts/hello-world.md',
            (new MarkdownPost('hello-world'))->getSourcePath()
        );
    }

    public function testGetOutputPath()
    {
        $this->assertSame(
            'posts/hello-world.html',
            (new MarkdownPost('hello-world'))->getOutputPath()
        );
    }

    public function testGetLink()
    {
        $this->assertSame(
            'posts/hello-world.html',
            (new MarkdownPost('hello-world'))->getLink()
        );
    }

    public function testMake()
    {
        $this->assertEquals(MarkdownPost::make(), new MarkdownPost());
    }

    public function testMakeWithData()
    {
        $this->assertEquals(
            MarkdownPost::make('foo', ['foo' => 'bar']),
            new MarkdownPost('foo', ['foo' => 'bar'])
        );
    }

    public function testShowInNavigation()
    {
        $this->assertFalse((new MarkdownPost())->showInNavigation());
    }

    public function testNavigationMenuPriority()
    {
        $this->assertSame(10, (new MarkdownPost())->navigationMenuPriority());
    }

    public function testNavigationMenuLabel()
    {
        $this->assertSame('Foo', (new MarkdownPost('foo'))->navigationMenuLabel());
    }

    public function testNavigationMenuGroup()
    {
        $this->assertNull((new MarkdownPost('foo'))->navigationMenuGroup());
    }

    public function testGetBladeView()
    {
        $this->assertSame('hyde::layouts/post', (new MarkdownPost('foo'))->getBladeView());
    }

    public function testFiles()
    {
        $this->assertSame([], MarkdownPost::files());
    }

    public function testData()
    {
        $this->assertSame('foo', (new MarkdownPost('foo'))->data('identifier'));
    }

    public function testGet()
    {
        $this->file(MarkdownPost::sourcePath('foo'));
        $this->assertEquals(new MarkdownPost('foo'), MarkdownPost::get('foo'));
    }

    public function testParse()
    {
        $this->file(MarkdownPost::sourcePath('foo'));
        $this->assertInstanceOf(MarkdownPost::class, MarkdownPost::parse('foo'));
    }

    public function testGetRouteKey()
    {
        $this->assertSame('posts/foo', (new MarkdownPost('foo'))->getRouteKey());
    }

    public function testTitle()
    {
        $markdownPost = new MarkdownPost('foo');
        $this->assertSame('HydePHP - Foo', $markdownPost->title());
    }

    public function testAll()
    {
        $this->assertInstanceOf(PageCollection::class, MarkdownPost::all());
    }

    public function testMetadata()
    {
        $this->assertInstanceOf(PageMetadataBag::class, (new MarkdownPost())->metadata());
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(MarkdownPost::class, new MarkdownPost());
    }

    public function testGetRoute()
    {
        $this->assertInstanceOf(Route::class, (new MarkdownPost())->getRoute());
    }

    public function testGetIdentifier()
    {
        $this->assertSame('foo', (new MarkdownPost('foo'))->getIdentifier());
    }

    public function testHas()
    {
        $this->assertTrue((new MarkdownPost('foo'))->has('identifier'));
    }

    public function testToCoreDataObject()
    {
        $this->assertInstanceOf(CoreDataObject::class, (new MarkdownPost('foo'))->toCoreDataObject());
    }

    public function testCompile()
    {
        $page = new MarkdownPost('foo');
        Hyde::shareViewData($page);
        $this->assertIsString(MarkdownPost::class, $page->compile());
    }

    public function testMatter()
    {
        $this->assertInstanceOf(FrontMatter::class, (new MarkdownPost('foo'))->matter());
    }

    public function testMarkdown()
    {
        $this->assertInstanceOf(Markdown::class, (new MarkdownPost('foo'))->markdown());
    }

    public function testSave()
    {
        $page = new MarkdownPost('foo');
        $this->assertSame($page, $page->save());
        $this->assertFileExists('_posts/foo.md');
        Filesystem::unlink('_posts/foo.md');
    }
}
