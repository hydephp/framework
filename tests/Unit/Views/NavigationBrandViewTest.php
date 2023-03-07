<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit\Views;

use Hyde\Framework\Features\Navigation\NavigationMenu;
use Hyde\Testing\TestCase;

/**
 * @see resources/views/components/navigation/navigation-brand.blade.php
 */
class NavigationBrandViewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRoute();
        $this->mockPage();
    }

    protected function render(): string
    {
        return view('hyde::components.navigation.navigation-brand', [
            'navigation' => NavigationMenu::create(),
        ])->render();
    }

    public function test_component_links_to_home_route()
    {
        $this->assertStringContainsString('href="index.html"', $this->render());
    }

    public function test_component_uses_site_name()
    {
        $this->assertStringContainsString('HydePHP', $this->render());
        config(['hyde.name' => 'foo']);
        $this->assertStringContainsString('foo', $this->render());
    }
}
