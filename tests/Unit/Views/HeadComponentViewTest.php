<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit\Views;

use Hyde\Hyde;
use Hyde\Pages\InMemoryPage;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\Blade;

/**
 * @see resources/views/layouts/styles.blade.php
 */
class HeadComponentViewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockPage();
    }

    protected function renderTestView(): string
    {
        return Blade::render($this->escapeIncludes(file_get_contents(Hyde::vendorPath('resources/views/layouts/head.blade.php'))));
    }

    public function testComponentCanBeRendered()
    {
        $this->mockPage();
        $this->assertStringContainsString('<meta charset="utf-8">', $this->renderTestView());
    }

    public function testTitleElementUsesPageTitle()
    {
        $page = $this->createMock(InMemoryPage::class);
        $page->method('title')->willReturn('Foo Bar');
        $this->mockPage($page);

        $this->assertStringContainsString('<title>Foo Bar</title>', $this->renderTestView());
    }

    public function testLinkToFaviconIsNotAddedWhenFileDoesNotExist()
    {
        $this->assertStringNotContainsString('favicon', $this->renderTestView());
    }

    public function testLinkToFaviconIsAddedWhenFileExists()
    {
        $this->mockPage();
        $this->file('_media/favicon.ico');

        $this->assertStringContainsString('<link rel="shortcut icon" href="media/favicon.ico" type="image/x-icon">', $this->renderTestView());
    }

    public function testLinkToFaviconUsesRelativeUrl()
    {
        $this->file('_media/favicon.ico');
        $this->mockPage(currentPage: 'foo/bar');

        $this->assertStringContainsString('<link rel="shortcut icon" href="../media/favicon.ico" type="image/x-icon">', $this->renderTestView());
    }

    public function testComponentIncludesMetaView()
    {
        $this->assertStringContainsString("@include('hyde::layouts.meta')", $this->renderTestView());
    }

    public function testComponentIncludesStylesView()
    {
        $this->assertStringContainsString("@include('hyde::layouts.styles')", $this->renderTestView());
    }

    protected function escapeIncludes(string $contents): string
    {
        return str_replace('@include', '@@include', $contents);
    }
}
