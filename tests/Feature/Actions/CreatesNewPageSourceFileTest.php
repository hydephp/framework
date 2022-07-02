<?php

namespace Hyde\Framework\Testing\Feature\Actions;

use Hyde\Framework\Actions\CreatesNewPageSourceFile;
use Hyde\Framework\Exceptions\FileConflictException;
use Hyde\Framework\Exceptions\UnsupportedPageTypeException;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Pages\BladePage;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Framework\Actions\CreatesNewPageSourceFile
 */
class CreatesNewPageSourceFileTest extends TestCase
{
    protected function tearDown(): void
    {
        if (file_exists(Hyde::path('_pages/682072b-test-page.md'))) {
            unlink(Hyde::path('_pages/682072b-test-page.md'));
        }

        if (file_exists(Hyde::path('_pages/682072b-test-page.blade.php'))) {
            unlink(Hyde::path('_pages/682072b-test-page.blade.php'));
        }

        parent::tearDown();
    }

    public function test_class_can_be_instantiated()
    {
        $this->assertInstanceOf(
            CreatesNewPageSourceFile::class,
            new CreatesNewPageSourceFile('682072b Test Page')
        );
    }

    public function test_that_a_slug_is_generated_from_the_title()
    {
        $this->assertEquals(
            '682072b-test-page',
            (new CreatesNewPageSourceFile('682072b Test Page'))->slug
        );
    }

    public function test_that_an_exception_is_thrown_for_invalid_page_type()
    {
        $this->expectException(UnsupportedPageTypeException::class);
        $this->expectExceptionMessage('The page type must be either "markdown", "blade", or "documentation"');

        (new CreatesNewPageSourceFile('682072b Test Page', 'invalid'));
    }

    public function test_that_an_exception_is_thrown_if_file_already_exists_and_overwrite_is_false()
    {
        $path = Hyde::path('_pages/foo.md');
        file_put_contents($path, 'foo');

        $this->expectException(FileConflictException::class);
        $this->expectExceptionMessage("File already exists: $path");
        $this->expectExceptionCode(409);

        new CreatesNewPageSourceFile('foo');

        unlink($path);
    }

    public function test_that_can_save_file_returns_true_if_file_already_exists_and_overwrite_is_true()
    {
        $path = Hyde::path('_pages/foo.md');
        file_put_contents($path, 'foo');

        new CreatesNewPageSourceFile('foo', force: true);

        $this->assertTrue(true);
        unlink($path);
    }

    public function test_that_a_markdown_file_can_be_created_and_contains_expected_content()
    {
        (new CreatesNewPageSourceFile('682072b Test Page'));

        $this->assertFileExists(
            Hyde::path('_pages/682072b-test-page.md')
        );

        $this->assertEquals(
            "---\ntitle: 682072b Test Page\n---\n\n# 682072b Test Page\n",
            file_get_contents(Hyde::path('_pages/682072b-test-page.md'))
        );
    }

    public function test_that_a_blade_file_can_be_created_and_contains_expected_content()
    {
        (new CreatesNewPageSourceFile('682072b Test Page', BladePage::class));

        $this->assertFileExists(
            Hyde::path('_pages/682072b-test-page.blade.php')
        );

        $fileContent = file_get_contents(Hyde::path('_pages/682072b-test-page.blade.php'));
        $this->assertStringContainsString(
            '@extends(\'hyde::layouts.app\')',
            $fileContent
        );
        $this->assertStringContainsString(
            '@php($title = "682072b Test Page")',
            $fileContent
        );
        $this->assertStringContainsString(
            '<h1 class="text-center text-3xl font-bold">682072b Test Page</h1>',
            $fileContent
        );
    }

    public function test_that_a_documentation_file_can_be_created_and_contains_expected_content()
    {
        (new CreatesNewPageSourceFile('682072b Test Page', DocumentationPage::class));

        $this->assertFileExists(
            Hyde::path('_docs/682072b-test-page.md')
        );

        $this->assertEquals(
            "# 682072b Test Page\n",
            file_get_contents(Hyde::path('_docs/682072b-test-page.md'))
        );
    }

    public function test_that_the_file_path_can_be_returned()
    {
        $this->assertEquals(
            Hyde::path('_pages/682072b-test-page.md'),
            (new CreatesNewPageSourceFile('682072b Test Page'))->outputPath
        );

        $this->assertEquals(
            Hyde::path('_pages/682072b-test-page.blade.php'),
            (new CreatesNewPageSourceFile('682072b Test Page', BladePage::class))->outputPath
        );
    }
}
