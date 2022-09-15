<?php

namespace Hyde\Framework\Testing\Unit;

use Hyde\Framework\Concerns\HydePage;
use Hyde\Framework\Models\Navigation\NavItem;
use Hyde\Framework\Models\Support\Route;
use Hyde\Testing\TestCase;

/**
 * This unit test covers the basics of the NavItem class.
 * For the full feature test, see the NavigationMenuTest class.
 *
 * @covers \Hyde\Framework\Models\Navigation\NavItem
 */
class NavItemTest extends TestCase
{
    public function test__construct()
    {
        $route = $this->createMock(Route::class);
        $route->method('getSourceModel')->willReturn($this->createMock(HydePage::class));
        $route->method('getLink')->willReturn('/');

        $item = new NavItem($route, 'Test', 500, true);

        $this->assertSame($route, $item->route);
        $this->assertSame('Test', $item->label);
        $this->assertSame(500, $item->priority);
        $this->assertTrue($item->hidden);
    }

    public function testFromRoute()
    {
        $route = Route::get('index');
        $item = NavItem::fromRoute($route);

        $this->assertSame($route, $item->route);
        $this->assertSame('Home', $item->label);
        $this->assertSame(0, $item->priority);
        $this->assertFalse($item->hidden);
    }

    public function testResolveLink()
    {
        $route = Route::get('index');
        $item = NavItem::fromRoute($route);

        $this->assertSame('index.html', $item->resolveLink());
    }

    public function test__toString()
    {
        $route = Route::get('index');
        $item = NavItem::fromRoute($route);

        $this->assertSame('index.html', (string) $item);
    }

    public function testToLink()
    {
        $item = NavItem::toLink('foo', 'bar', 10);

        $this->assertSame('foo', $item->href);
        $this->assertSame('bar', $item->label);
        $this->assertSame(10, $item->priority);
        $this->assertFalse($item->hidden);
    }

    public function testToRoute()
    {
        $route = Route::get('index');
        $item = NavItem::toRoute($route, 'foo', 10);

        $this->assertSame($route, $item->route);
        $this->assertSame('foo', $item->label);
        $this->assertSame(10, $item->priority);
        $this->assertFalse($item->hidden);
    }

    public function testIsCurrentRoute()
    {
        $route = Route::get('index');
        $item = NavItem::fromRoute($route);

        $this->assertTrue($item->isCurrent($route->getSourceModel()));
    }

    public function testIsCurrentLink()
    {
        $item = NavItem::toLink('index.html', 'Home');

        $this->assertTrue($item->isCurrent(Route::get('index')->getSourceModel()));
    }
}
