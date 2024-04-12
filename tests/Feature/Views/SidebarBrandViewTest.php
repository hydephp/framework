<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Feature\Views;

use Hyde\Hyde;
use Hyde\Testing\TestCase;
use Hyde\Foundation\HydeKernel;
use Hyde\Testing\TestsBladeViews;
use Hyde\Pages\DocumentationPage;

class SidebarBrandViewTest extends TestCase
{
    use TestsBladeViews;

    public function testSidebarBrandView()
    {
        $view = $this->test(view('hyde::components.docs.sidebar-brand'));

        $view->assertSee('HydePHP Docs');
        $view->assertSee('theme-toggle-button');
        $view->assertDontSee('href');
    }

    public function testSidebarBrandViewWithHomeRoute()
    {
        Hyde::routes()->addRoute((new DocumentationPage('index'))->getRoute());

        $view = $this->test(view('hyde::components.docs.sidebar-brand'));

        $view->assertSee('HydePHP Docs');
        $view->assertSee('theme-toggle-button');
        $view->assertSeeHtml('<a href="docs/index.html">HydePHP Docs</a>', true);
    }

    public function testSidebarBrandViewWithDefaultHeaderText()
    {
        config(['docs.sidebar' => []]);

        $view = $this->test(view('hyde::components.docs.sidebar-brand'));

        $view->assertSee('Documentation');
        $view->assertDontSee('HydePHP Docs');
    }

    public function testSidebarBrandViewWithDefaultHeaderTextAndHomeRoute()
    {
        Hyde::routes()->addRoute((new DocumentationPage('index'))->getRoute());

        config(['docs.sidebar' => []]);

        $view = $this->test(view('hyde::components.docs.sidebar-brand'));

        $view->assertSee('Documentation');
        $view->assertSeeHtml('<a href="docs/index.html">Documentation</a>', true);
        $view->assertDontSee('HydePHP Docs');
    }

    public function testSidebarBrandViewWithoutDarkmodeFeature()
    {
        $mock = $this->mock(HydeKernel::class)->makePartial();
        $mock->shouldReceive('hasFeature')->with('darkmode')->andReturn(false);
        HydeKernel::setInstance($mock);

        $view = $this->test(view('hyde::components.docs.sidebar-brand'));

        $view->assertSee('HydePHP Docs');
        $view->assertDontSee('theme-toggle-button');
    }
}
