<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit\Views;

use Hyde\Foundation\Facades\Routes;
use Hyde\Framework\Features\Navigation\NavItem;
use Hyde\Testing\TestCase;

/**
 * @see resources/views/components/navigation/navigation-link.blade.php
 */
class NavigationLinkViewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRoute();
        $this->mockPage();
    }

    protected function render(?NavItem $item = null): string
    {
        return view('hyde::components.navigation.navigation-link', [
            'item' => $item ?? NavItem::forLink('foo.html', 'Foo'),
        ])->render();
    }

    public function test_component_links_to_route_destination()
    {
        $this->assertStringContainsString('href="foo.html"', $this->render());
    }

    public function test_component_uses_title()
    {
        $this->assertStringContainsString('Foo', $this->render());
    }

    public function test_component_is_current_when_current_route_matches()
    {
        $this->mockRoute(Routes::get('index'));
        $this->assertStringContainsString('current', $this->render(NavItem::forRoute(Routes::get('index'), 'Home')));
    }

    public function test_component_has_aria_current_when_current_route_matches()
    {
        $this->mockRoute(Routes::get('index'));
        $this->assertStringContainsString('aria-current="page"', $this->render(NavItem::forRoute(Routes::get('index'), 'Home')));
    }
}
