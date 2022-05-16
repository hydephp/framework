<?php

namespace Tests\Unit;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Models\MarkdownPost;
use Tests\TestCase;

/**
 * Class FluentPathHelpersTest.
 *
 * @covers \Hyde\Framework\Concerns\Internal\FluentPathHelpers
 */
class FluentPathHelpersTest extends TestCase
{
    public function test_get_model_source_path_method_returns_path_for_model_classes()
    {
        $this->assertEquals(
            Hyde::path('_posts'),
            Hyde::getModelSourcePath(MarkdownPost::class)
        );

        $this->assertEquals(
            Hyde::path('_pages'),
            Hyde::getModelSourcePath(MarkdownPage::class)
        );

        $this->assertEquals(
            Hyde::path('_docs'),
            Hyde::getModelSourcePath(DocumentationPage::class)
        );

        $this->assertEquals(
            Hyde::path('_pages'),
            Hyde::getModelSourcePath(BladePage::class)
        );
    }

    public function test_get_model_source_path_method_returns_path_to_file_for_model_classes()
    {
        $this->assertEquals(
            Hyde::path('_posts'.DIRECTORY_SEPARATOR.'foo.md'),
            Hyde::getModelSourcePath(MarkdownPost::class, 'foo.md')
        );

        $this->assertEquals(
            Hyde::path('_pages'.DIRECTORY_SEPARATOR.'foo.md'),
            Hyde::getModelSourcePath(MarkdownPage::class, 'foo.md')
        );

        $this->assertEquals(
            Hyde::path('_docs'.DIRECTORY_SEPARATOR.'foo.md'),
            Hyde::getModelSourcePath(DocumentationPage::class, 'foo.md')
        );

        $this->assertEquals(
            Hyde::path('_pages'.DIRECTORY_SEPARATOR.'foo.md'),
            Hyde::getModelSourcePath(BladePage::class, 'foo.md')
        );
    }

    public function test_helper_for_blade_pages()
    {
        $this->assertEquals(
            Hyde::path('_pages'),
            Hyde::getBladePagePath()
        );
    }

    public function test_helper_for_markdown_pages()
    {
        $this->assertEquals(
            Hyde::path('_pages'),
            Hyde::getMarkdownPagePath()
        );
    }

    public function test_helper_for_markdown_posts()
    {
        $this->assertEquals(
            Hyde::path('_posts'),
            Hyde::getMarkdownPostPath()
        );
    }

    public function test_helper_for_documentation_pages()
    {
        $this->assertEquals(
            Hyde::path('_docs'),
            Hyde::getDocumentationPagePath()
        );
    }

    public function test_helper_for_site_output_path()
    {
        $this->assertEquals(
            Hyde::path('_site'),
            Hyde::getSiteOutputPath()
        );
    }

    public function test_helper_for_site_output_path_returns_path_to_file_within_the_directory()
    {
        $this->assertEquals(
            Hyde::path('_site'.DIRECTORY_SEPARATOR.'foo.html'),
            Hyde::getSiteOutputPath('foo.html')
        );
    }
}
