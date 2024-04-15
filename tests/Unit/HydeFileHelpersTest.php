<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit;

use Hyde\Foundation\Facades\Routes;
use Hyde\Hyde;
use Hyde\Support\Facades\Render;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Foundation\HydeKernel
 */
class HydeFileHelpersTest extends TestCase
{
    public function testCurrentPageReturnsCurrentPageViewProperty()
    {
        Render::share('routeKey', 'foo');
        $this->assertSame('foo', Hyde::currentRouteKey());
    }

    public function testCurrentPageFallsBackToNullStringIfCurrentPageViewPropertyIsNotSet()
    {
        $this->assertNull(Hyde::currentRouteKey());
    }

    public function testCurrentRouteReturnsCurrentRouteViewProperty()
    {
        Render::share('route', Routes::get('index'));
        $this->assertSame(Routes::get('index'), Hyde::currentRoute());
    }

    public function testCurrentRouteFallsBackToNullIfCurrentRouteViewPropertyIsNotSet()
    {
        $this->assertNull(Hyde::currentRoute());
    }
}
