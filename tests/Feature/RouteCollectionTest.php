<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Feature;

use Hyde\Foundation\Kernel\RouteCollection;
use Hyde\Framework\Exceptions\RouteNotFoundException;
use Hyde\Hyde;
use Hyde\Pages\BladePage;
use Hyde\Pages\DocumentationPage;
use Hyde\Pages\HtmlPage;
use Hyde\Pages\MarkdownPage;
use Hyde\Pages\MarkdownPost;
use Hyde\Support\Models\Route;
use Hyde\Testing\TestCase;
use Illuminate\Support\Collection;

/**
 * @covers \Hyde\Foundation\Kernel\RouteCollection
 * @covers \Hyde\Foundation\Concerns\BaseFoundationCollection
 */
class RouteCollectionTest extends TestCase
{
    public function test_boot_method_discovers_all_pages()
    {
        $collection = RouteCollection::init(Hyde::getInstance())->boot();

        $this->assertInstanceOf(RouteCollection::class, $collection);
        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertEquals([
            '404' => new Route(new BladePage('404')),
            'index' => new Route(new BladePage('index')),
        ], $collection->all());
    }

    public function test_boot_method_discovers_all_page_types()
    {
        $this->withoutDefaultPages();

        $this->file('_pages/blade.blade.php');
        $this->file('_pages/markdown.md');
        $this->file('_pages/html.html');
        $this->file('_posts/post.md');
        $this->file('_docs/docs.md');

        $collection = Hyde::routes();

        $this->assertInstanceOf(RouteCollection::class, $collection);
        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertEquals([
            'blade' => new Route(new BladePage('blade')),
            'markdown' => new Route(new MarkdownPage('markdown')),
            'html' => new Route(new HtmlPage('html')),
            'posts/post' => new Route(new MarkdownPost('post')),
            'docs/docs' => new Route(new DocumentationPage('docs')),
        ], $collection->all());

        $this->restoreDefaultPages();
    }

    public function test_get_routes_returns_all_routes()
    {
        $this->file('_pages/blade.blade.php');
        $this->file('_pages/markdown.md');
        $this->file('_pages/html.html');
        $this->file('_posts/post.md');
        $this->file('_docs/docs.md');

        $this->assertSame(Hyde::routes(), Hyde::routes()->getRoutes());
    }

    public function test_get_routes_for_model_returns_collection_of_routes_of_given_class()
    {
        $this->withoutDefaultPages();

        $this->file('_pages/blade.blade.php');
        $this->file('_pages/markdown.md');
        $this->file('_pages/html.html');
        $this->file('_posts/post.md');
        $this->file('_docs/docs.md');

        $collection = Hyde::routes();

        $this->assertCount(5, $collection);
        $this->assertEquals(new Route(new BladePage('blade')), $collection->getRoutes(BladePage::class)->first());
        $this->assertEquals(new Route(new MarkdownPage('markdown')), $collection->getRoutes(MarkdownPage::class)->first());
        $this->assertEquals(new Route(new MarkdownPost('post')), $collection->getRoutes(MarkdownPost::class)->first());
        $this->assertEquals(new Route(new DocumentationPage('docs')), $collection->getRoutes(DocumentationPage::class)->first());
        $this->assertEquals(new Route(new HtmlPage('html')), $collection->getRoutes(HtmlPage::class)->first());

        $this->restoreDefaultPages();
    }

    public function test_add_route_adds_new_route()
    {
        $collection = Hyde::routes();
        $this->assertCount(2, $collection);
        $collection->addRoute(new Route(new BladePage('new')));
        $this->assertCount(3, $collection);
        $this->assertEquals(new Route(new BladePage('new')), $collection->last());
    }

    public function test_get_route()
    {
        $this->assertEquals(new Route(new BladePage('index')), Hyde::routes()->getRoute('index'));
    }

    public function test_get_route_with_non_existing_route()
    {
        $this->expectException(RouteNotFoundException::class);
        Hyde::routes()->getRoute('non-existing');
    }
}
