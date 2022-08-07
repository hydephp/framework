<?php

namespace Hyde\Framework\Testing\Feature;

use Hyde\Framework\Contracts\AbstractMarkdownPage;
use Hyde\Framework\Contracts\AbstractPage;
use Hyde\Framework\Contracts\PageContract;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Markdown;
use Hyde\Framework\Models\Pages\BladePage;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\Models\Pages\MarkdownPage;
use Hyde\Framework\Models\Pages\MarkdownPost;
use Hyde\Framework\Models\Route;
use Hyde\Testing\TestCase;

/**
 * Test the AbstractPage class.
 *
 * Since the class is abstract, we can't test it directly,
 * so we will use the MarkdownPage class as a proxy,
 * since it's the simplest implementation.
 *
 * @covers \Hyde\Framework\Contracts\AbstractPage
 * @covers \Hyde\Framework\Contracts\AbstractMarkdownPage
 * @covers \Hyde\Framework\Actions\Constructors\FindsNavigationDataForPage
 *
 * @see \Hyde\Framework\Testing\Unit\AbstractPageMetadataTest
 */
class AbstractPageTest extends TestCase
{
    protected function tearDown(): void
    {
        BladePage::$sourceDirectory = '_pages';
        MarkdownPage::$sourceDirectory = '_pages';
        MarkdownPost::$sourceDirectory = '_posts';
        DocumentationPage::$sourceDirectory = '_docs';
        MarkdownPage::$fileExtension = '.md';

        parent::tearDown();
    }

    public function test_get_source_directory_returns_static_property()
    {
        MarkdownPage::$sourceDirectory = 'foo';
        $this->assertEquals('foo', MarkdownPage::getSourceDirectory());
    }

    public function test_get_source_directory_trims_trailing_slashes()
    {
        MarkdownPage::$sourceDirectory = '/foo/\\';
        $this->assertEquals('foo', MarkdownPage::getSourceDirectory());
    }

    public function test_get_output_directory_returns_static_property()
    {
        MarkdownPage::$outputDirectory = 'foo';
        $this->assertEquals('foo', MarkdownPage::getOutputDirectory());
    }

    public function test_get_output_directory_trims_trailing_slashes()
    {
        MarkdownPage::$outputDirectory = '/foo/\\';
        $this->assertEquals('foo', MarkdownPage::getOutputDirectory());
    }

    public function test_get_file_extension_returns_static_property()
    {
        MarkdownPage::$fileExtension = '.foo';
        $this->assertEquals('.foo', MarkdownPage::getFileExtension());
    }

    public function test_get_file_extension_forces_leading_period()
    {
        MarkdownPage::$fileExtension = 'foo';
        $this->assertEquals('.foo', MarkdownPage::getFileExtension());
    }

    public function test_parse_parses_supplied_slug_into_a_page_model()
    {
        Hyde::touch(('_pages/foo.md'));

        $this->assertInstanceOf(MarkdownPage::class, $page = MarkdownPage::parse('foo'));
        $this->assertEquals('foo', $page->identifier);

        unlink(Hyde::path('_pages/foo.md'));
    }

    public function test_files_returns_array_of_source_files()
    {
        Hyde::touch(('_pages/foo.md'));
        $this->assertEquals(['foo'], MarkdownPage::files());
        unlink(Hyde::path('_pages/foo.md'));
    }

    public function test_all_returns_collection_of_all_source_files_parsed_into_the_model()
    {
        Hyde::touch(('_pages/foo.md'));
        $this->assertEquals(
            collect([tap(new MarkdownPage('foo'), function ($page) {
                $page->title = 'Foo';
            })]),
            MarkdownPage::all()
        );
        unlink(Hyde::path('_pages/foo.md'));
    }

    public function test_qualify_basename_properly_expands_basename_for_the_model()
    {
        $this->assertEquals('_pages/foo.md', MarkdownPage::qualifyBasename('foo'));
    }

    public function test_qualify_basename_trims_slashes_from_input()
    {
        $this->assertEquals('_pages/foo.md', MarkdownPage::qualifyBasename('/foo/\\'));
    }

    public function test_qualify_basename_uses_the_static_properties()
    {
        MarkdownPage::$sourceDirectory = 'foo';
        MarkdownPage::$fileExtension = 'txt';
        $this->assertEquals('foo/bar.txt', MarkdownPage::qualifyBasename('bar'));
    }

    public function test_get_output_location_returns_the_file_output_path_for_the_supplied_basename()
    {
        $this->assertEquals('foo.html', MarkdownPage::getOutputLocation('foo'));
    }

    public function test_get_output_location_returns_the_configured_location()
    {
        MarkdownPage::$outputDirectory = 'foo';
        $this->assertEquals('foo/bar.html', MarkdownPage::getOutputLocation('bar'));
    }

    public function test_get_output_location_trims_trailing_slashes_from_directory_setting()
    {
        MarkdownPage::$outputDirectory = '/foo/\\';
        $this->assertEquals('foo/bar.html', MarkdownPage::getOutputLocation('bar'));
    }

    public function test_get_output_location_trims_trailing_slashes_from_basename()
    {
        $this->assertEquals('foo.html', MarkdownPage::getOutputLocation('/foo/\\'));
    }

    public function test_get_current_page_path_returns_output_directory_and_basename()
    {
        $page = new MarkdownPage('foo');
        $this->assertEquals('foo', $page->getCurrentPagePath());
    }

    public function test_get_current_page_path_returns_output_directory_and_basename_for_configured_directory()
    {
        MarkdownPage::$outputDirectory = 'foo';
        $page = new MarkdownPage('bar');
        $this->assertEquals('foo/bar', $page->getCurrentPagePath());
    }

    public function test_get_current_page_path_trims_trailing_slashes_from_directory_setting()
    {
        MarkdownPage::$outputDirectory = '/foo/\\';
        $page = new MarkdownPage('bar');
        $this->assertEquals('foo/bar', $page->getCurrentPagePath());
    }

    public function test_get_output_path_returns_current_page_path_with_html_extension_appended()
    {
        $page = new MarkdownPage('foo');
        $this->assertEquals('foo.html', $page->getOutputPath());
    }

    public function test_get_source_path_returns_qualified_basename()
    {
        $this->assertEquals(
            MarkdownPage::qualifyBasename('foo'),
            (new MarkdownPage('foo'))->getSourcePath()
        );
    }

    public function test_markdown_page_implements_page_contract()
    {
        $this->assertInstanceOf(PageContract::class, new MarkdownPage());
    }

    public function test_all_page_models_extend_abstract_page()
    {
        $pages = [
            MarkdownPage::class,
            MarkdownPost::class,
            DocumentationPage::class,
        ];

        foreach ($pages as $page) {
            $this->assertInstanceOf(AbstractPage::class, new $page());
        }

        $this->assertInstanceOf(AbstractPage::class, new BladePage('foo'));
    }

    public function test_all_page_models_have_configured_source_directory()
    {
        $pages = [
            BladePage::class => '_pages',
            MarkdownPage::class => '_pages',
            MarkdownPost::class => '_posts',
            DocumentationPage::class => '_docs',
        ];

        foreach ($pages as $page => $expected) {
            $this->assertEquals($expected, $page::$sourceDirectory);
        }
    }

    public function test_all_page_models_have_configured_output_directory()
    {
        $pages = [
            BladePage::class => '',
            MarkdownPage::class => '',
            MarkdownPost::class => 'posts',
            DocumentationPage::class => 'docs',
        ];

        foreach ($pages as $page => $expected) {
            $this->assertEquals($expected, $page::$outputDirectory);
        }
    }

    public function test_all_page_models_have_configured_file_extension()
    {
        $pages = [
            BladePage::class => '.blade.php',
            MarkdownPage::class => '.md',
            MarkdownPost::class => '.md',
            DocumentationPage::class => '.md',
        ];

        foreach ($pages as $page => $expected) {
            $this->assertEquals($expected, $page::$fileExtension);
        }
    }

    public function test_abstract_markdown_page_extends_abstract_page()
    {
        $this->assertInstanceOf(AbstractPage::class, new class extends AbstractMarkdownPage {});
    }

    public function test_abstract_markdown_page_implements_page_contract()
    {
        $this->assertInstanceOf(PageContract::class, new class extends AbstractMarkdownPage {});
    }

    public function test_abstract_markdown_page_has_markdown_document_property()
    {
        $this->assertClassHasAttribute('markdown', AbstractMarkdownPage::class);
    }

    public function test_abstract_markdown_page_has_file_extension_property()
    {
        $this->assertClassHasAttribute('fileExtension', AbstractMarkdownPage::class);
    }

    public function test_abstract_markdown_page_file_extension_property_is_set_to_md()
    {
        $this->assertEquals('.md', AbstractMarkdownPage::$fileExtension);
    }

    public function test_abstract_markdown_page_constructor_arguments_are_optional()
    {
        $page = new class extends AbstractMarkdownPage {};
        $this->assertInstanceOf(AbstractMarkdownPage::class, $page); // If we get this far, we're good as no exception was thrown
    }

    public function test_abstract_markdown_page_constructor_assigns_markdown_document_property_if_set()
    {
        $markdown = new Markdown();
        $page = new MarkdownPage(markdown: $markdown);
        $this->assertSame($markdown, $page->markdown);
    }

    public function test_abstract_markdown_page_constructor_creates_new_markdown_document_if_no_markdown_document_is_set()
    {
        $page = new MarkdownPage();
        $this->assertInstanceOf(Markdown::class, $page->markdown);
    }

    public function test_abstract_markdown_page_markdown_helper_returns_the_markdown_document_instance()
    {
        $page = new MarkdownPage();
        $this->assertSame($page->markdown, $page->markdown());
    }

    public function test_abstract_markdown_page_markdown_helper_returns_the_configured_markdown_document_instance()
    {
        $markdown = new Markdown();
        $page = new MarkdownPage(markdown: $markdown);
        $this->assertSame($markdown, $page->markdown());
    }

    public function test_abstract_markdown_page_make_helper_constructs_dynamic_title_automatically()
    {
        $page = MarkdownPage::make('', ['title' => 'Foo']);
        $this->assertEquals('Foo', $page->title);
    }

    public function test_markdown_based_pages_extend_abstract_markdown_page()
    {
        $pages = [
            MarkdownPage::class,
            MarkdownPost::class,
            DocumentationPage::class,
        ];

        foreach ($pages as $page) {
            $this->assertInstanceOf(AbstractMarkdownPage::class, new $page());
        }
    }

    public function test_blade_pages_do_not_extend_abstract_markdown_page()
    {
        $this->assertNotInstanceOf(AbstractMarkdownPage::class, new BladePage('foo'));
    }

    public function test_get_route_returns_page_route()
    {
        $page = new MarkdownPage();
        $this->assertEquals(new Route($page), $page->getRoute());
    }

    public function test_html_title_returns_site_name_plus_page_title()
    {
        $this->assertEquals('HydePHP - Foo', MarkdownPage::make('', ['title' => 'Foo'])->htmlTitle());
    }

    public function test_html_title_uses_configured_site_name()
    {
        config(['site.name' => 'Foo Bar']);
        $this->assertEquals('Foo Bar - Foo', (new MarkdownPage('Foo'))->htmlTitle());
    }

    public function test_body_helper_returns_markdown_document_body_in_markdown_pages()
    {
        $page = new MarkdownPage(markdown: new Markdown(body: '# Foo'));
        $this->assertEquals('# Foo', $page->markdown->body());
    }

    public function test_show_in_navigation_returns_false_for_markdown_post()
    {
        $page = MarkdownPost::make();

        $this->assertFalse($page->showInNavigation());
    }

    public function test_show_in_navigation_returns_true_for_documentation_page_if_slug_is_index()
    {
        $page = DocumentationPage::make('index');

        $this->assertTrue($page->showInNavigation());
    }

    public function test_show_in_navigation_returns_false_for_documentation_page_if_slug_is_not_index()
    {
        $page = DocumentationPage::make('not-index');

        $this->assertFalse($page->showInNavigation());
    }

    public function test_show_in_navigation_returns_false_for_abstract_markdown_page_if_matter_navigation_hidden_is_true()
    {
        $page = MarkdownPage::make('foo', ['navigation.hidden' => true]);

        $this->assertFalse($page->showInNavigation());
    }

    public function test_show_in_navigation_returns_true_for_abstract_markdown_page_if_matter_navigation_hidden_is_false()
    {
        $page = MarkdownPage::make('foo', ['navigation.hidden' => false]);

        $this->assertTrue($page->showInNavigation());
    }

    public function test_show_in_navigation_returns_true_for_abstract_markdown_page_if_matter_navigation_hidden_is_not_set()
    {
        $page = MarkdownPage::make('foo', ['navigation.hidden' => null]);

        $this->assertTrue($page->showInNavigation());
    }

    public function test_show_in_navigation_returns_false_if_slug_is_present_in_config_hyde_navigation_exclude()
    {
        $page = MarkdownPage::make('foo');
        $this->assertTrue($page->showInNavigation());

        config(['hyde.navigation.exclude' => ['foo']]);
        $page = MarkdownPage::make('foo');
        $this->assertFalse($page->showInNavigation());
    }

    public function test_show_in_navigation_returns_false_if_slug_is_404()
    {
        $page = MarkdownPage::make('404');
        $this->assertFalse($page->showInNavigation());
    }

    public function test_show_in_navigation_defaults_to_true_if_all_checks_pass()
    {
        $page = MarkdownPage::make('foo');
        $this->assertTrue($page->showInNavigation());
    }

    public function test_navigation_menu_priority_returns_front_matter_value_of_navigation_priority_if_abstract_markdown_page_and_not_null()
    {
        $page = MarkdownPage::make('foo', ['navigation.priority' => 1]);
        $this->assertEquals(1, $page->navigationMenuPriority());
    }

    public function test_navigation_menu_priority_returns_specified_config_value_if_slug_exists_in_config_hyde_navigation_order()
    {
        $page = MarkdownPage::make('foo');
        $this->assertEquals(999, $page->navigationMenuPriority());

        config(['hyde.navigation.order' => ['foo' => 1]]);
        $page = MarkdownPage::make('foo');
        $this->assertEquals(1, $page->navigationMenuPriority());
    }

    public function test_navigation_menu_priority_gives_precedence_to_front_matter_over_config_hyde_navigation_order()
    {
        $page = MarkdownPage::make('foo', ['navigation.priority' => 1]);

        $this->assertEquals(1, $page->navigationMenuPriority());

        config(['hyde.navigation.order' => ['foo' => 2]]);
        $this->assertEquals(1, $page->navigationMenuPriority());
    }

    public function test_navigation_menu_priority_returns_100_for_documentation_page()
    {
        $page = DocumentationPage::make('foo');
        $this->assertEquals(100, $page->navigationMenuPriority());
    }

    public function test_navigation_menu_priority_returns_0_if_slug_is_index()
    {
        $page = MarkdownPage::make('index');
        $this->assertEquals(0, $page->navigationMenuPriority());
    }

    public function test_navigation_menu_priority_does_not_return_0_if_slug_is_index_but_model_is_documentation_page()
    {
        $page = DocumentationPage::make('index');
        $this->assertEquals(100, $page->navigationMenuPriority());
    }

    public function test_navigation_menu_priority_returns_10_if_slug_is_posts()
    {
        $page = MarkdownPage::make('posts');
        $this->assertEquals(10, $page->navigationMenuPriority());
    }

    public function test_navigation_menu_priority_defaults_to_999_if_no_other_conditions_are_met()
    {
        $page = MarkdownPage::make('foo');
        $this->assertEquals(999, $page->navigationMenuPriority());
    }

    public function test_navigation_menu_title_returns_navigation_title_matter_if_set()
    {
        $page = MarkdownPage::make('foo', ['navigation.title' => 'foo']);
        $this->assertEquals('foo', $page->navigationMenuTitle());
    }

    public function test_navigation_menu_title_returns_title_matter_if_set()
    {
        $page = MarkdownPage::make('foo', ['title' => 'foo']);
        $this->assertEquals('foo', $page->navigationMenuTitle());
    }

    public function test_navigation_menu_title_navigation_title_has_precedence_over_title()
    {
        $page = MarkdownPage::make('foo', ['title' => 'foo', 'navigation.title' => 'bar']);
        $this->assertEquals('bar', $page->navigationMenuTitle());
    }

    public function test_navigation_menu_title_returns_docs_if_slug_is_index_and_model_is_documentation_page()
    {
        $page = DocumentationPage::make('index');
        $this->assertEquals('Docs', $page->navigationMenuTitle());
    }

    public function test_navigation_menu_title_returns_home_if_slug_is_index_and_model_is_not_documentation_page()
    {
        $page = MarkdownPage::make('index');
        $this->assertEquals('Home', $page->navigationMenuTitle());
    }

    public function test_navigation_menu_title_returns_title_if_title_is_set_and_not_empty()
    {
        $page = MarkdownPage::make('bar', ['title' => 'foo']);
        $this->assertEquals('foo', $page->navigationMenuTitle());
    }

    public function test_navigation_menu_title_falls_back_to_hyde_make_title_from_slug()
    {
        $page = MarkdownPage::make('foo');
        $this->assertEquals('Foo', $page->navigationMenuTitle());
    }
}
