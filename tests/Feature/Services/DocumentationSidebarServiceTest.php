<?php

namespace Tests\Feature\Services;

use Hyde\Framework\Actions\ConvertsArrayToFrontMatter;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\DocumentationSidebar;
use Hyde\Framework\Models\DocumentationSidebarItem;
use Hyde\Framework\Services\DocumentationSidebarService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Services\DocumentationSidebarService
 * @covers \Hyde\Framework\Models\DocumentationSidebarItem
 * @covers \Hyde\Framework\Models\DocumentationSidebar
 */
class DocumentationSidebarServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->resetDocsDirectory();
    }

    protected function tearDown(): void
    {
        $this->resetDocsDirectory();

        parent::tearDown();
    }

    public function test_sidebar_can_be_created()
    {
        $service = new DocumentationSidebarService();
        $sidebar = $service->createSidebar()->getSidebar();

        $this->assertInstanceOf(DocumentationSidebar::class, $sidebar);
    }

    public function test_sidebar_items_can_be_added()
    {
        $sidebar = DocumentationSidebarService::get()->addItem(
            new DocumentationSidebarItem('test', 'test')
        );

        $this->assertCount(1, $sidebar);
        $this->assertInstanceOf(DocumentationSidebarItem::class, $sidebar[0]);
    }

    public function test_sidebar_items_are_added_automatically()
    {
        $this->createTestFiles();

        $sidebar = DocumentationSidebarService::get();

        $this->assertCount(5, $sidebar);
    }

    public function test_index_page_is_removed_from_sidebar()
    {
        $this->createTestFiles();
        touch(Hyde::path('_docs/index.md'));

        $sidebar = DocumentationSidebarService::get();
        $this->assertCount(5, $sidebar);
    }

    public function test_files_with_front_matter_hidden_set_to_true_are_removed_from_sidebar()
    {
        $this->createTestFiles();
        File::put(Hyde::path('_docs/test.md'), "---\nhidden: true\n---\n\n# Foo");

        $sidebar = DocumentationSidebarService::get();
        $this->assertCount(5, $sidebar);
    }

    public function test_sidebar_is_ordered_alphabetically_when_no_order_is_set_in_config()
    {
        Config::set('hyde.documentationPageOrder', []);
        touch(Hyde::path('_docs/alpha.md'));
        touch(Hyde::path('_docs/bravo.md'));
        touch(Hyde::path('_docs/charlie.md'));

        $sidebar = DocumentationSidebarService::get();

        $this->assertEquals('alpha', $sidebar[0]->destination);
        $this->assertEquals('bravo', $sidebar[1]->destination);
        $this->assertEquals('charlie', $sidebar[2]->destination);
    }

    public function test_sidebar_is_ordered_by_priority_when_priority_is_set_in_config()
    {
        Config::set('hyde.documentationPageOrder', [
            'charlie',
            'bravo',
            'alpha',
        ]);
        touch(Hyde::path('_docs/alpha.md'));
        touch(Hyde::path('_docs/bravo.md'));
        touch(Hyde::path('_docs/charlie.md'));

        $sidebar = DocumentationSidebarService::get();
        $this->assertEquals('charlie', $sidebar[0]->destination);
        $this->assertEquals('bravo', $sidebar[1]->destination);
        $this->assertEquals('alpha', $sidebar[2]->destination);
    }

    public function test_sidebar_item_priority_can_be_set_in_front_matter()
    {
        file_put_contents(
            Hyde::path('_docs/foo.md'),
            (new ConvertsArrayToFrontMatter)->execute([
                'priority' => 25,
            ])
        );

        $this->assertEquals(25, DocumentationSidebarItem::parseFromFile('foo')->priority);
        $this->assertEquals(25, DocumentationSidebarService::get()->first()->priority);
    }

    public function test_sidebar_item_priority_set_in_config_overrides_front_matter()
    {
        file_put_contents(Hyde::path('_docs/foo.md'),
            (new ConvertsArrayToFrontMatter)->execute(['priority' => 25])
        );

        Config::set('hyde.documentationPageOrder', ['foo']);

        $this->assertEquals(25, DocumentationSidebarService::get()->first()->priority);
    }

    public function test_both_sidebar_priority_setting_methods_can_be_used()
    {
        Config::set('hyde.documentationPageOrder', [
            'first',
            'third',
            'second',
        ]);
        touch(Hyde::path('_docs/first.md'));
        touch(Hyde::path('_docs/second.md'));
        file_put_contents(Hyde::path('_docs/third.md'),
            (new ConvertsArrayToFrontMatter)->execute(['priority' => 3])
        );
        $sidebar = DocumentationSidebarService::get();
        $this->assertEquals('first', $sidebar[0]->destination);
        $this->assertEquals('second', $sidebar[1]->destination);
        $this->assertEquals('third', $sidebar[2]->destination);
    }

    protected function resetDocsDirectory(): void
    {
        File::deleteDirectory(Hyde::path('_docs'));
        mkdir(Hyde::path('_docs'));
    }

    protected function createTestFiles(int $count = 5): void
    {
        for ($i = 0; $i < $count; $i++) {
            touch(Hyde::path('_docs/test-'.$i.'.md'));
        }
    }
}
