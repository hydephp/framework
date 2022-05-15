<?php

namespace Tests\Feature;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\MarkdownPost;
use Hyde\Framework\Services\BuildService;
use Hyde\Framework\Services\CollectionService;
use Tests\TestCase;

/**
 * Class SourceDirectoriesCanBeChangedTest.
 */
class SourceDirectoriesCanBeChangedTest extends TestCase
{
    public function test_post_directory_baseline()
    {
        $this->assertEquals('_posts', MarkdownPost::$sourceDirectory);
    }

    public function test_posts_directory_can_be_changed()
    {
        MarkdownPost::$sourceDirectory = '_source/posts';
        $this->assertEquals('_source/posts', MarkdownPost::$sourceDirectory);
    }

    public function test_build_service_recognizes_changed_directory()
    {
        MarkdownPost::$sourceDirectory = '_source/posts';

        $this->assertEquals(
            '_source/posts',
            BuildService::getFilePathForModelClassFiles(MarkdownPost::class)
        );
    }

    public function test_autodiscovery_discovers_posts_in_changed_directory()
    {
        // Using a subdirectory in a directory we know exists, to make cleanup easier.
        mkdir(Hyde::path('_posts/test'));
        touch(Hyde::path('_posts/test/test.md'));
        
        MarkdownPost::$sourceDirectory = '_posts/test';

        $this->assertEquals(
            ['test'],
            CollectionService::getSourceFileListForModel(MarkdownPost::class)
        );

        unlink(Hyde::path('_posts/test/test.md'));
        rmdir(Hyde::path('_posts/test'));
    }
}
