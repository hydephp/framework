<?php

namespace Tests\Feature\Actions;

use Tests\TestCase;
use Hyde\Framework\Actions\GeneratesDocumentationSearchIndexFile as Action;
use Hyde\Framework\Hyde;

/**
 * @covers \Hyde\Framework\Actions\GeneratesDocumentationSearchIndexFile
 */
class GeneratesDocumentationSearchIndexFileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        unlinkIfExists(Action::$filePath);
    }

    protected function tearDown(): void
    {
        unlinkIfExists(Action::$filePath);

        parent::tearDown();
    }

    // Test it generates a JSON file with a search index
    public function test_it_generates_a_JSON_file_with_a_search_index()
    {
        touch(Hyde::path('_docs/foo.md'));
        touch(Hyde::path('_docs/bar.md'));

        $expected = [
            [
                'slug' => 'bar',
                'title' => 'Bar',
            ],
            [
                'slug' => 'foo',
                'title' => 'Foo',
            ],
        ];

        Action::run();
        
        $this->assertEquals(
            json_encode($expected), file_get_contents(Action::$filePath)
        );

        unlink(Hyde::path('_docs/foo.md'));
        unlink(Hyde::path('_docs/bar.md'));
    }

    // Test it handles generation even when there are no pages
    public function test_it_handles_generation_even_when_there_are_no_pages()
    {
        Action::run();

        $this->assertEquals(
            '[]', file_get_contents(Action::$filePath)
        );
    }

    // Test save method saves the file to the correct location.
    public function test_save_method_saves_the_file_to_the_correct_location()
    {
        (new Action())->save();

        $this->assertFileExists('_site/docs/searchIndex.json');
    }

    // Test generatePageObject method generates a page object.
    public function test_generate_page_object_method_generates_a_page_object()
    {
        $expected = new \stdClass;
        $expected->slug = "foo";
        $expected->title = "Bar";

        file_put_contents(Hyde::path('_docs/foo.md'), "# Bar\n\n Hello World");

        $this->assertEquals(
            $expected, (new Action())->generatePageObject('foo')
        );

        unlink(Hyde::path('_docs/foo.md'));
    }

    // Test getSourceFileSlugs returns valid array for source files
    public function test_get_source_file_slugs_returns_valid_array_for_source_files()
    {
        touch(Hyde::path('_docs/a.md'));
        touch(Hyde::path('_docs/b.md'));
        touch(Hyde::path('_docs/c.md'));

        $this->assertEquals(
            ['a', 'b', 'c'], (new Action())->getSourceFileSlugs()
        );

        unlink(Hyde::path('_docs/a.md'));
        unlink(Hyde::path('_docs/b.md'));
        unlink(Hyde::path('_docs/c.md'));
    }

    // Test getSourceFileSlugs returns empty array when no source files exists
    public function test_get_source_file_slugs_returns_empty_array_when_no_source_files_exists()
    {
        $this->assertEquals(
            [], (new Action())->getSourceFileSlugs()
        );
    }

    // Test it generates a valid JSON 
    public function test_it_generates_a_valid_JSON()
    {
        file_put_contents(Hyde::path('_docs/foo.md'), "# Bar\n\n Hello World");
        file_put_contents(Hyde::path('_docs/bar.md'), "# Foo\n\n Hello World");


        $this->assertEquals(
            '[{"slug":"bar","title":"Foo"},{"slug":"foo","title":"Bar"}]',
            (new Action())->generate()->getJson()
        );

        unlink(Hyde::path('_docs/foo.md'));
        unlink(Hyde::path('_docs/bar.md'));
    }
}
