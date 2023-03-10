<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Feature;

use Hyde\Pages\MarkdownPage;
use Hyde\Support\Facades\Render;
use Hyde\Support\Models\RenderData;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\View;
use InvalidArgumentException;

/**
 * @covers \Hyde\Support\Models\RenderData
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

    public function testGetRoute()
    {
        $this->assertNull(Render::getRoute());

        Render::setPage($page = new MarkdownPage());
        $this->assertEquals($page->getRoute(), Render::getRoute());
    }

    public function testGetRouteKey()
    {
        $this->assertNull(Render::getRouteKey());

        Render::setPage($page = new MarkdownPage());
        $this->assertSame($page->getRouteKey(), Render::getRouteKey());
    }

    public function testShareToView()
    {
        $this->assertNull(View::shared('page'));
        $this->assertNull(View::shared('route'));
        $this->assertNull(View::shared('routeKey'));

        Render::setPage($page = new MarkdownPage());

        $this->assertSame($page, View::shared('page'));
        $this->assertEquals($page->getRoute(), View::shared('route'));
        $this->assertSame($page->getRouteKey(), View::shared('routeKey'));
    }

    public function testShare()
    {
        $this->assertNull(Render::getRouteKey());

        Render::share('routeKey', 'bar');
        $this->assertSame('bar', Render::getRouteKey());
    }

    public function testShareInvalidProperty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Property 'foo' does not exist on Hyde\Support\Models\Render");

        Render::share('foo', 'bar');
    }

    public function testShareCascadesDataToView()
    {
        $this->assertNull(View::shared('routeKey'));

        Render::share('routeKey', 'bar');
        $this->assertSame('bar', View::shared('routeKey'));
    }

    public function testClearData()
    {
        $render = new RenderData();
        $render->setPage(new MarkdownPage());
        $this->assertNotNull($render->getPage());

        $render->clearData();
        $this->assertNull($render->getPage());
    }

    public function testClearDataOnFacade()
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
        $this->assertNotNull(View::shared('route'));
        $this->assertNotNull(View::shared('routeKey'));

        Render::clearData();
        $this->assertNull(View::shared('page'));
        $this->assertNull(View::shared('route'));
        $this->assertNull(View::shared('routeKey'));
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
            'route' => null,
            'routeKey' => null,
        ], $render->toArray());

        Render::setPage($page = new MarkdownPage());
        $this->assertEquals([
            'render' => $render,
            'page' => $page,
            'route' => $page->getRoute(),
            'routeKey' => $page->getRouteKey(),
        ], $render->toArray());
    }
}
