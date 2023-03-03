<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Feature;

use BadMethodCallException;
use Hyde\Facades\Filesystem;
use function collect;
use function config;
use Hyde\Foundation\Facades\Routes;
use Hyde\Framework\Features\Navigation\DropdownNavItem;
use Hyde\Framework\Features\Navigation\NavigationMenu;
use Hyde\Framework\Features\Navigation\NavItem;
use Hyde\Pages\DocumentationPage;
use Hyde\Pages\MarkdownPage;
use Hyde\Pages\MarkdownPost;
use Hyde\Facades\Route;
use Hyde\Testing\TestCase;
use Illuminate\Support\Collection;

/**
 * @covers \Hyde\Framework\Features\Navigation\NavigationMenu
 * @covers \Hyde\Framework\Features\Navigation\BaseNavigationMenu
 */
class NavigationMenuTest extends TestCase
{
    public function test_constructor()
    {
        $menu = new NavigationMenu();

        $this->assertInstanceOf(NavigationMenu::class, $menu);
    }

    public function test_generate_method_creates_collection_of_nav_items()
    {
        $menu = new NavigationMenu();

        $this->assertInstanceOf(Collection::class, $menu->items);
        $this->assertEmpty($menu->items);
    }

    public function test_generate_method_adds_route_items()
    {
        $menu = new NavigationMenu();
        $menu->generate();

        $expected = collect([
            '404' => NavItem::fromRoute(Route::get('404')),
            'index' => NavItem::fromRoute(Route::get('index')),
        ]);

        $this->assertCount(count($expected), $menu->items);
        $this->assertEquals($expected, $menu->items);
    }

    public function test_sort_method_sorts_items_by_priority()
    {
        $menu = new NavigationMenu();
        $menu->generate()->sort();

        $expected = collect([
            NavItem::fromRoute(Route::get('index')),
            NavItem::fromRoute(Route::get('404')),
        ]);

        $this->assertCount(count($expected), $menu->items);
        $this->assertEquals($expected, $menu->items);
    }

    public function test_filter_method_removes_items_with_hidden_property_set_to_true()
    {
        $menu = new NavigationMenu();
        $menu->generate()->filter();

        $expected = collect([
            NavItem::fromRoute(Route::get('index')),
        ]);

        $this->assertCount(count($expected), $menu->items);
        $this->assertEquals($expected, $menu->items);
    }

    public function test_static_create_method_creates_new_processed_collection()
    {
        Filesystem::touch('_pages/foo.md');
        $menu = NavigationMenu::create();

        $this->assertInstanceOf(NavigationMenu::class, $menu);
        $this->assertEquals(
            (new NavigationMenu())->generate()->filter()->sort(),
            NavigationMenu::create()
        );
    }

    public function test_created_collection_is_sorted_by_navigation_menu_priority()
    {
        Filesystem::touch('_pages/foo.md');
        Filesystem::touch('_docs/index.md');

        $menu = NavigationMenu::create();

        $expected = collect([
            NavItem::fromRoute(Route::get('index')),
            NavItem::fromRoute(Route::get('foo')),
            NavItem::fromRoute(Route::get('docs/index')),
        ]);

        $this->assertCount(count($expected), $menu->items);
        $this->assertEquals($expected, $menu->items);

        Filesystem::unlink('_pages/foo.md');
        Filesystem::unlink('_docs/index.md');
    }

    public function test_is_sorted_automatically_when_using_navigation_menu_create()
    {
        Filesystem::touch('_pages/foo.md');

        $menu = NavigationMenu::create();

        $expected = collect([
            NavItem::fromRoute(Route::get('index')),
            NavItem::fromRoute(Route::get('foo')),
        ]);

        $this->assertCount(count($expected), $menu->items);
        $this->assertEquals($expected, $menu->items);

        Filesystem::unlink('_pages/foo.md');
    }

    public function test_collection_only_contains_nav_items()
    {
        $this->assertContainsOnlyInstancesOf(NavItem::class, NavigationMenu::create()->items);
    }

    public function test_external_link_can_be_added_in_config()
    {
        config(['hyde.navigation.custom' => [NavItem::toLink('https://example.com', 'foo')]]);

        $menu = NavigationMenu::create();

        $expected = collect([
            NavItem::fromRoute(Route::get('index')),
            NavItem::toLink('https://example.com', 'foo'),
        ]);

        $this->assertCount(count($expected), $menu->items);
        $this->assertEquals($expected, $menu->items);
    }

    public function test_path_link_can_be_added_in_config()
    {
        config(['hyde.navigation.custom' => [NavItem::toLink('foo', 'foo')]]);

        $menu = NavigationMenu::create();

        $expected = collect([
            NavItem::fromRoute(Route::get('index')),
            NavItem::toLink('foo', 'foo'),
        ]);

        $this->assertCount(count($expected), $menu->items);
        $this->assertEquals($expected, $menu->items);
    }

    public function test_duplicates_are_removed_when_adding_in_config()
    {
        config(['hyde.navigation.custom' => [
            NavItem::toLink('foo', 'foo'),
            NavItem::toLink('foo', 'foo'),
        ]]);

        $menu = NavigationMenu::create();

        $expected = collect([
            NavItem::fromRoute(Route::get('index')),
            NavItem::toLink('foo', 'foo'),
        ]);

        $this->assertCount(count($expected), $menu->items);
        $this->assertEquals($expected, $menu->items);
    }

    public function test_duplicates_are_removed_when_adding_in_config_regardless_of_destination()
    {
        config(['hyde.navigation.custom' => [
            NavItem::toLink('foo', 'foo'),
            NavItem::toLink('bar', 'foo'),
        ]]);

        $menu = NavigationMenu::create();

        $expected = collect([
            NavItem::fromRoute(Route::get('index')),
            NavItem::toLink('foo', 'foo'),
        ]);

        $this->assertCount(count($expected), $menu->items);
        $this->assertEquals($expected, $menu->items);
    }

    public function test_documentation_pages_that_are_not_index_are_not_added_to_the_menu()
    {
        Filesystem::touch('_docs/foo.md');
        Filesystem::touch('_docs/index.md');

        $menu = NavigationMenu::create();

        $expected = collect([
            NavItem::fromRoute(Route::get('index')),
            NavItem::fromRoute(Route::get('docs/index')),
        ]);

        $this->assertCount(count($expected), $menu->items);
        $this->assertEquals($expected, $menu->items);

        Filesystem::unlink('_docs/foo.md');
        Filesystem::unlink('_docs/index.md');
    }

    public function test_pages_in_subdirectories_are_not_added_to_the_navigation_menu()
    {
        $this->directory('_pages/foo');
        Filesystem::touch('_pages/foo/bar.md');

        $menu = NavigationMenu::create();
        $expected = collect([NavItem::fromRoute(Route::get('index'))]);

        $this->assertCount(count($expected), $menu->items);
        $this->assertEquals($expected, $menu->items);
    }

    public function test_pages_in_subdirectories_can_be_added_to_the_navigation_menu_with_config_flat_setting()
    {
        config(['hyde.navigation.subdirectories' => 'flat']);
        $this->directory('_pages/foo');
        Filesystem::touch('_pages/foo/bar.md');

        $menu = NavigationMenu::create();
        $expected = collect([
            NavItem::fromRoute(Route::get('index')),
            NavItem::fromRoute(Route::get('foo/bar')),
        ]);

        $this->assertCount(count($expected), $menu->items);
        $this->assertEquals($expected, $menu->items);
    }

    public function test_pages_in_subdirectories_are_not_added_to_the_navigation_menu_with_config_dropdown_setting()
    {
        config(['hyde.navigation.subdirectories' => 'dropdown']);
        $this->directory('_pages/foo');
        Filesystem::touch('_pages/foo/bar.md');

        $menu = NavigationMenu::create();
        $expected = collect([
            NavItem::fromRoute(Route::get('index')),
            DropdownNavItem::fromArray('foo', [
                NavItem::fromRoute(Route::get('foo/bar')),
            ]),
        ]);

        $this->assertCount(count($expected), $menu->items);
        $this->assertEquals($expected, $menu->items);
    }

    public function test_has_dropdowns_returns_false_when_there_are_no_dropdowns()
    {
        config(['hyde.navigation.subdirectories' => 'dropdown']);
        $menu = NavigationMenu::create();
        $this->assertFalse($menu->hasDropdowns());
    }

    public function test_has_dropdowns_returns_true_when_there_are_dropdowns()
    {
        config(['hyde.navigation.subdirectories' => 'dropdown']);
        $menu = NavigationMenu::create();
        $menu->items->push(NavItem::fromRoute((new MarkdownPage('foo/bar'))->getRoute()));
        $menu->generate();
        $this->assertTrue($menu->hasDropdowns());
    }

    public function test_has_dropdowns_always_returns_false_when_dropdowns_are_disabled()
    {
        $menu = NavigationMenu::create();
        $menu->items->push(NavItem::fromRoute((new MarkdownPage('foo/bar'))->getRoute()));
        $menu->generate();
        $this->assertFalse($menu->hasDropdowns());
    }

    public function test_get_dropdowns_returns_empty_array_there_are_no_dropdowns()
    {
        config(['hyde.navigation.subdirectories' => 'dropdown']);
        $menu = NavigationMenu::create();
        $this->assertCount(0, $menu->getDropdowns());
        $this->assertSame([], $menu->getDropdowns());
    }

    public function test_get_dropdowns_returns_correct_array_when_there_are_dropdowns()
    {
        config(['hyde.navigation.subdirectories' => 'dropdown']);
        $menu = NavigationMenu::create();
        $menu->items->push(NavItem::fromRoute((new MarkdownPage('foo/bar'))->getRoute()));
        $menu->generate();
        $this->assertCount(1, $menu->getDropdowns());

        $this->assertEquals([
            'dropdown.foo' => DropdownNavItem::fromArray('foo', [
                NavItem::fromRoute((new MarkdownPage('foo/bar'))->getRoute()),
            ]), ], $menu->getDropdowns());
    }

    public function test_get_dropdowns_with_multiple_items()
    {
        config(['hyde.navigation.subdirectories' => 'dropdown']);
        $menu = NavigationMenu::create();

        $menu->items->push(NavItem::fromRoute((new MarkdownPage('foo/bar'))->getRoute()));
        $menu->items->push(NavItem::fromRoute((new MarkdownPage('foo/baz'))->getRoute()));
        $menu->generate();

        $this->assertCount(1, $menu->getDropdowns());

        $this->assertEquals([
            'dropdown.foo' => DropdownNavItem::fromArray('foo', [
                NavItem::fromRoute((new MarkdownPage('foo/bar'))->getRoute()),
                NavItem::fromRoute((new MarkdownPage('foo/baz'))->getRoute()),
            ]),
        ], $menu->getDropdowns());
    }

    public function test_get_dropdowns_with_multiple_dropdowns()
    {
        config(['hyde.navigation.subdirectories' => 'dropdown']);
        $menu = NavigationMenu::create();

        $menu->items->push(NavItem::fromRoute((new MarkdownPage('foo/bar'))->getRoute()));
        $menu->items->push(NavItem::fromRoute((new MarkdownPage('foo/baz'))->getRoute()));
        $menu->items->push(NavItem::fromRoute((new MarkdownPage('cat/hat'))->getRoute()));
        $menu->generate();

        $this->assertCount(2, $menu->getDropdowns());

        $this->assertEquals([
            'dropdown.foo' => DropdownNavItem::fromArray('foo', [
                NavItem::fromRoute((new MarkdownPage('foo/bar'))->getRoute()),
                NavItem::fromRoute((new MarkdownPage('foo/baz'))->getRoute()),
            ]),
            'dropdown.cat' => DropdownNavItem::fromArray('cat', [
                NavItem::fromRoute((new MarkdownPage('cat/hat'))->getRoute()),
            ]),
        ], $menu->getDropdowns());
    }

    public function test_get_dropdowns_throws_exception_when_disabled()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Dropdowns are not enabled. Enable it by setting `hyde.navigation.subdirectories` to `dropdown`.');

        $menu = NavigationMenu::create();
        $menu->getDropdowns();
    }

    public function test_documentation_pages_do_not_get_added_to_dropdowns()
    {
        config(['hyde.navigation.subdirectories' => 'dropdown']);
        $menu = NavigationMenu::create();

        $menu->items->push(NavItem::fromRoute((new DocumentationPage('foo'))->getRoute()));
        $menu->items->push(NavItem::fromRoute((new DocumentationPage('bar/baz'))->getRoute()));
        $menu->generate();

        $this->assertFalse($menu->hasDropdowns());
        $this->assertCount(0, $menu->getDropdowns());
    }

    public function test_blog_posts_do_not_get_added_to_dropdowns()
    {
        config(['hyde.navigation.subdirectories' => 'dropdown']);
        $menu = NavigationMenu::create();

        $menu->items->push(NavItem::fromRoute((new MarkdownPost('foo'))->getRoute()));
        $menu->items->push(NavItem::fromRoute((new MarkdownPost('bar/baz'))->getRoute()));
        $menu->generate();

        $this->assertFalse($menu->hasDropdowns());
        $this->assertCount(0, $menu->getDropdowns());
    }

    public function test_pages_in_dropdowns_do_not_get_added_to_the_main_navigation()
    {
        config(['hyde.navigation.subdirectories' => 'dropdown']);

        Routes::push((new MarkdownPage('foo'))->getRoute());
        Routes::push((new MarkdownPage('bar/baz'))->getRoute());
        $menu = NavigationMenu::create();

        $this->assertCount(3, $menu->items);
        $this->assertEquals([
            NavItem::fromRoute(Route::get('index')),
            NavItem::fromRoute((new MarkdownPage('foo'))->getRoute()),
            DropdownNavItem::fromArray('bar', [
                NavItem::fromRoute((new MarkdownPage('bar/baz'))->getRoute()),
            ]),
        ], $menu->items->all());
    }

    // TODO test dropdown items are sorted
}
