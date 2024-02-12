<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit\Views;

use Hyde\Facades\Filesystem;
use Hyde\Hyde;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\Blade;

/**
 * @see resources/views/layouts/scripts.blade.php
 */
class ScriptsComponentViewTest extends TestCase
{
    protected ?string $mockCurrentPage = null;

    protected function renderTestView(): string
    {
        config(['hyde.enable_cache_busting' => false]);
        $this->mockCurrentPage($this->mockCurrentPage ?? '');

        return Blade::render(file_get_contents(
            Hyde::vendorPath('resources/views/layouts/scripts.blade.php')
        ));
    }

    public function test_component_can_be_rendered()
    {
        $this->assertStringContainsString('<script defer', $this->renderTestView());
    }

    public function test_component_has_link_to_app_js_file_when_it_exists()
    {
        Filesystem::touch('_media/app.js');
        $this->assertStringContainsString('<script defer src="media/app.js"', $this->renderTestView());
        Filesystem::unlink('_media/app.js');
    }

    public function test_component_does_not_render_link_to_app_js_when_it_does_not_exist()
    {
        $this->assertStringNotContainsString('<script defer src="media/app.js"', $this->renderTestView());
    }

    public function test_component_uses_relative_path_to_app_js_file_for_nested_pages()
    {
        Filesystem::touch('_media/app.js');
        $this->mockCurrentPage = 'foo';
        $this->assertStringContainsString('<script defer src="media/app.js"', $this->renderTestView());
        $this->mockCurrentPage = 'foo/bar';
        $this->assertStringContainsString('<script defer src="../media/app.js"', $this->renderTestView());
        $this->mockCurrentPage = 'foo/bar/cat.html';
        $this->assertStringContainsString('<script defer src="../../media/app.js"', $this->renderTestView());
        $this->mockCurrentPage = null;
        Filesystem::unlink('_media/app.js');
    }

    public function testCanAddScriptsHtmlFromConfigHook()
    {
        config(['hyde.scripts' => '<script src="custom-hook.js"></script>']);

        $this->assertStringContainsString('<script src="custom-hook.js"></script>', $this->renderTestView());
    }

    public function testCanAddScriptsHtmlFromHtmlInclude()
    {
        $this->file('resources/includes/scripts.html', '<script src="html-include.js"></script>');

        $this->assertStringContainsString('<script src="html-include.js"></script>', $this->renderTestView());
    }

    public function test_scripts_can_be_pushed_to_the_component_scripts_stack()
    {
        view()->share('routeKey', '');

        $this->assertStringContainsString('foo bar',
            Blade::render('
                @push("scripts")
                foo bar
                @endpush

                @include("hyde::layouts.scripts")'
            )
        );
    }
}
