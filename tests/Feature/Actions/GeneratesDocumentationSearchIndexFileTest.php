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
            $expected,
            (new Action())->generatePageObject('foo')
        );

        unlink(Hyde::path('_docs/foo.md'));
    }

    // Test it generates a JSON file with a search index

    // Test it handles generation even when there are no pages
}
