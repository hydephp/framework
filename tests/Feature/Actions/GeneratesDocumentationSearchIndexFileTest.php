<?php

namespace Hyde\Framework\Testing\Feature\Actions;

use Hyde\Framework\Actions\GeneratesDocumentationSearchIndexFile as Action;
use Hyde\Framework\Hyde;
use Hyde\Testing\TestCase;

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

    public function test_it_generates_a_json_file_with_a_search_index()
    {
        deleteDirectory(Hyde::path('_docs'));
        mkdir(Hyde::path('_docs'));

        touch(Hyde::path('_docs/foo.md'));

        $expected = [
            [
                'slug' => 'foo',
                'title' => 'Foo',
                'content' => '',
                'destination' => 'foo.html',
            ],
        ];

        Action::run();

        $this->assertEquals(
            json_encode($expected), file_get_contents(Action::$filePath)
        );

        unlink(Hyde::path('_docs/foo.md'));
    }

    public function test_it_adds_all_files_to_search_index()
    {
        touch(Hyde::path('_docs/foo.md'));
        touch(Hyde::path('_docs/bar.md'));
        touch(Hyde::path('_docs/baz.md'));

        $this->assertCount(3, (new Action())->generate()->searchIndex);

        unlink(Hyde::path('_docs/foo.md'));
        unlink(Hyde::path('_docs/bar.md'));
        unlink(Hyde::path('_docs/baz.md'));
    }

    public function test_it_handles_generation_even_when_there_are_no_pages()
    {
        Action::run();

        $this->assertEquals(
            '[]', file_get_contents(Action::$filePath)
        );
    }

    public function test_save_method_saves_the_file_to_the_correct_location()
    {
        (new Action())->save();

        $this->assertFileExists('_site/docs/search.json');
    }

    public function test_generate_page_object_method_generates_a_page_object()
    {
        $expected = new \stdClass;
        $expected->slug = 'foo';
        $expected->title = 'Bar';
        $expected->content = "Bar \n Hello World";
        $expected->destination = 'foo.html';

        file_put_contents(Hyde::path('_docs/foo.md'), "# Bar\n\n Hello World");

        $this->assertEquals(
            $expected, (new Action())->generatePageObject('foo')
        );

        unlink(Hyde::path('_docs/foo.md'));
    }

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

    public function test_get_source_file_slugs_returns_empty_array_when_no_source_files_exists()
    {
        $this->assertEquals(
            [], (new Action())->getSourceFileSlugs()
        );
    }

    public function test_it_generates_a_valid_JSON()
    {
        file_put_contents(Hyde::path('_docs/foo.md'), "# Bar\n\n Hello World");
        file_put_contents(Hyde::path('_docs/bar.md'), "# Foo\n\n Hello World");

        $this->assertEquals(
            '[{"slug":"bar","title":"Foo","content":"Foo \n Hello World","destination":"bar.html"},'.
            '{"slug":"foo","title":"Bar","content":"Bar \n Hello World","destination":"foo.html"}]',
            (new Action())->generate()->getJson()
        );

        unlink(Hyde::path('_docs/foo.md'));
        unlink(Hyde::path('_docs/bar.md'));
    }

    public function test_get_destination_for_slug_returns_empty_string_for_index_when_pretty_url_is_enabled()
    {
        config(['hyde.pretty_urls' => true]);

        $this->assertEquals(
            '', (new Action())->getDestinationForSlug('index')
        );
    }

    public function test_get_destination_for_slug_returns_pretty_url_when_enabled()
    {
        config(['hyde.pretty_urls' => true]);

        $this->assertEquals(
            'foo', (new Action())->getDestinationForSlug('foo')
        );
    }

    public function test_excluded_pages_are_not_present_in_the_search_index()
    {
        touch(Hyde::path('_docs/excluded.md'));
        config(['docs.exclude_from_search' => ['excluded']]);

        $this->assertNotContains('excluded', (new Action())->getSourceFileSlugs());
        $this->assertStringNotContainsString('excluded', (new Action())->generate()->getJson());

        unlink(Hyde::path('_docs/excluded.md'));
    }
}
