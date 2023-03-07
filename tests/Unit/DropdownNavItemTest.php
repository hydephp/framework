<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit;

use Hyde\Framework\Features\Navigation\DropdownNavItem;
use Hyde\Framework\Features\Navigation\NavItem;
use Hyde\Pages\MarkdownPage;
use Hyde\Support\Facades\Render;
use Hyde\Support\Models\RenderData;
use Hyde\Support\Models\Route;
use Hyde\Testing\UnitTestCase;

/**
 * @covers \Hyde\Framework\Features\Navigation\DropdownNavItem
 */
class DropdownNavItemTest extends UnitTestCase
{
    protected static bool $needsKernel = true;
    protected static bool $needsConfig = true;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        Render::swap(new RenderData());
    }

    public static function tearDownAfterClass(): void
    {
        Render::swap(new RenderData());
    }

    public function testConstruct()
    {
        $item = new DropdownNavItem('foo', []);

        $this->assertSame('foo', $item->label);
        $this->assertSame([], $item->items);
    }

    public function testFromArray()
    {
        $item = DropdownNavItem::fromArray('foo', []);

        $this->assertSame('foo', $item->label);
        $this->assertSame([], $item->items);
    }

    public function testWithChildren()
    {
        $children = [
            new NavItem(new Route(new MarkdownPage()), 'bar'),
        ];

        $item = new DropdownNavItem('foo', $children);
        $this->assertSame($children, $item->items);

        $item = DropdownNavItem::fromArray('foo', $children);
        $this->assertSame($children, $item->items);
    }

    public function testGetItems()
    {
        $children = [
            new NavItem(new Route(new MarkdownPage()), 'bar'),
        ];

        $item = new DropdownNavItem('foo', $children);
        $this->assertSame($children, $item->getItems()->all());

        $item = DropdownNavItem::fromArray('foo', $children);
        $this->assertSame($children, $item->getItems()->all());
    }
}
