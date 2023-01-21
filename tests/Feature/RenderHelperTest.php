<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Feature;

use Hyde\Pages\MarkdownPage;
use Hyde\Support\Facades\Render;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\View;
use InvalidArgumentException;

/**
 * @covers \Hyde\Support\Models\Render
 * @covers \Hyde\Support\Facades\Render
 */
class RenderHelperTest extends TestCase
{
    public function testSetAndGetPage()
    {
        $this->assertNull(Render::getPage());

        Render::setPage($page = new MarkdownPage());
        $this->assertSame($page, Render::getPage());
    }

    public function testSetPageSharesDataToViewAutomatically()
    {
        $this->assertNull(View::shared('page'));

        Render::setPage($page = new MarkdownPage());
        $this->assertSame($page, View::shared('page'));
    }

    public function testGetCurrentRoute()
    {
        $this->assertNull(Render::getCurrentRoute());

        Render::setPage($page = new MarkdownPage());
        $this->assertEquals($page->getRoute(), Render::getCurrentRoute());
    }

    public function testGetCurrentPage()
    {
        $this->assertNull(Render::getCurrentPage());

        Render::setPage($page = new MarkdownPage());
        $this->assertSame($page->getRouteKey(), Render::getCurrentPage());
    }

    public function testShareToView()
    {
        $this->assertNull(View::shared('page'));
        $this->assertNull(View::shared('currentRoute'));
        $this->assertNull(View::shared('currentPage'));

        Render::setPage($page = new MarkdownPage());

        $this->assertSame($page, View::shared('page'));
        $this->assertEquals($page->getRoute(), View::shared('currentRoute'));
        $this->assertSame($page->getRouteKey(), View::shared('currentPage'));
    }

    public function testShare()
    {
        $this->assertNull(Render::getCurrentPage());

        Render::share('currentPage', 'bar');
        $this->assertSame('bar', Render::getCurrentPage());
    }

    public function testShareInvalidProperty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Property 'foo' does not exist on Hyde\Support\Models\Render");

        Render::share('foo', 'bar');
    }

    public function testShareCascadesDataToView()
    {
        $this->assertNull(View::shared('currentPage'));

        Render::share('currentPage', 'bar');
        $this->assertSame('bar', View::shared('currentPage'));
    }

    public function testClearData()
    {
        Render::setPage(new MarkdownPage());
        $this->assertNotNull(Render::getPage());

        Render::clearData();
        $this->assertNull(Render::getPage());
    }

    public function testClearDataCascadesToClearItsViewData()
    {
        Render::setPage(new MarkdownPage());

        $this->assertNotNull(View::shared('page'));
        $this->assertNotNull(View::shared('currentRoute'));
        $this->assertNotNull(View::shared('currentPage'));

        Render::clearData();
        $this->assertNull(View::shared('page'));
        $this->assertNull(View::shared('currentRoute'));
        $this->assertNull(View::shared('currentPage'));
    }

    public function testClearDataDoesNotClearOtherViewData()
    {
        View::share('foo', 'bar');
        $this->assertNotNull(View::shared('foo'));

        Render::clearData();
        $this->assertNotNull(View::shared('foo'));
    }

    public function testClearDataDoesNotClearRenderInstanceFromViewData()
    {
        Render::shareToView();
        $this->assertNotNull(View::shared('render'));

        Render::clearData();
        $this->assertNotNull(View::shared('render'));
    }

    public function testToArray()
    {
        $render = Render::getFacadeRoot();
        $this->assertSame([
            'render' => $render,
            'page' => null,
            'currentRoute' => null,
            'currentPage' => null,
        ], $render->toArray());

        Render::setPage($page = new MarkdownPage());
        $this->assertEquals([
            'render' => $render,
            'page' => $page,
            'currentRoute' => $page->getRoute(),
            'currentPage' => $page->getRouteKey(),
        ], $render->toArray());
    }
}
