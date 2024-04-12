<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Feature;

use Hyde\Enums\Feature;
use Hyde\Facades\Features;
use Hyde\Pages\DocumentationPage;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\Config;

/**
 * @covers \Hyde\Facades\Features::darkmode
 * @covers \Hyde\Facades\Features::hasDarkmode
 */
class DarkmodeFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRoute();
        $this->mockPage();
    }

    public function testHasDarkmode()
    {
        Config::set('hyde.features', []);

        $this->assertFalse(Features::hasDarkmode());

        Config::set('hyde.features', [
            Feature::Darkmode,
        ]);

        $this->assertTrue(Features::hasDarkmode());
    }

    public function testLayoutHasToggleButtonAndScriptWhenEnabled()
    {
        Config::set('hyde.features', [
            Feature::MarkdownPages,
            Feature::BladePages,
            Feature::Darkmode,
        ]);

        $view = view('hyde::layouts/page')->with([
            'title' => 'foo',
            'content' => 'foo',
            'routeKey' => 'foo',
        ])->render();

        $this->assertStringContainsString('title="Toggle theme"', $view);
        $this->assertStringContainsString('<script>if (localStorage.getItem(\'color-theme\') === \'dark\'', $view);
    }

    public function testDocumentationPageHasToggleButtonAndScriptWhenEnabled()
    {
        Config::set('hyde.features', [
            Feature::DocumentationPages,
            Feature::Darkmode,
        ]);

        view()->share('page', new DocumentationPage());

        $view = view('hyde::layouts/docs')->with([
            'title' => 'foo',
            'content' => 'foo',
            'routeKey' => 'foo',
        ])->render();

        $this->assertStringContainsString('title="Toggle theme"', $view);
        $this->assertStringContainsString('<script>if (localStorage.getItem(\'color-theme\') === \'dark\'', $view);
    }

    public function testDarkModeThemeButtonIsHiddenInLayoutsWhenDisabled()
    {
        Config::set('hyde.features', [
            Feature::MarkdownPages,
            Feature::BladePages,
        ]);

        $view = view('hyde::layouts/page')->with([
            'title' => 'foo',
            'content' => 'foo',
            'routeKey' => 'foo',
        ])->render();

        $this->assertStringNotContainsString('title="Toggle theme"', $view);
        $this->assertStringNotContainsString('<script>if (localStorage.getItem(\'color-theme\') === \'dark\'', $view);
    }

    public function testDarkModeThemeButtonIsHiddenInDocumentationPagesWhenDisabled()
    {
        Config::set('hyde.features', [
            Feature::DocumentationPages,
        ]);

        view()->share('page', new DocumentationPage());

        $view = view('hyde::layouts/docs')->with([
            'title' => 'foo',
            'content' => 'foo',
            'routeKey' => 'foo',
        ])->render();

        $this->assertStringNotContainsString('title="Toggle theme"', $view);
        $this->assertStringNotContainsString('<script>if (localStorage.getItem(\'color-theme\') === \'dark\'', $view);
    }
}
